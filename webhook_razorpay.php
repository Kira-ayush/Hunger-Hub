<?php

/**
 * Razorpay Webhook Handler for HungerHub
 * 
 * This file handles webhook notifications from Razorpay
 * Place this file at: https://yourdomain.com/HungerHub/webhook_razorpay.php
 */

require_once 'db.php';
require_once 'payment_config.php';

// Get webhook payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// Get webhook secret
$razorpay_config = getPaymentConfig('RAZORPAY');
$webhook_secret = $razorpay_config['webhook_secret'];

// Verify webhook signature
function verifyWebhookSignature($payload, $signature, $secret)
{
    $expected_signature = hash_hmac('sha256', $payload, $secret);
    return hash_equals($expected_signature, $signature);
}

// Log webhook events
function logWebhookEvent($event_type, $payment_id, $order_id, $status, $data = [])
{
    global $conn;

    $stmt = $conn->prepare("
        INSERT INTO payment_logs (
            payment_id, order_id, log_type, message, request_data, response_data, created_at
        ) VALUES (?, ?, 'webhook', ?, ?, ?, NOW())
    ");

    $message = "Webhook: $event_type - Status: $status";
    $request_data = json_encode($_SERVER);
    $response_data = json_encode($data);

    $stmt->bind_param("sisss", $payment_id, $order_id, $message, $request_data, $response_data);
    $stmt->execute();
}

// Verify signature
if (!verifyWebhookSignature($payload, $signature, $webhook_secret)) {
    http_response_code(400);
    die('Invalid signature');
}

// Parse webhook data
$data = json_decode($payload, true);

if (!$data || !isset($data['event'])) {
    http_response_code(400);
    die('Invalid payload');
}

$event = $data['event'];
$payment_data = $data['payload']['payment']['entity'] ?? [];
$order_data = $data['payload']['order']['entity'] ?? [];

// Extract important information
$razorpay_payment_id = $payment_data['id'] ?? '';
$razorpay_order_id = $payment_data['order_id'] ?? '';
$amount = $payment_data['amount'] ?? 0;
$status = $payment_data['status'] ?? '';
$method = $payment_data['method'] ?? '';

// Get our order ID from notes or order description
$our_order_id = null;
if (isset($payment_data['notes']['order_id'])) {
    $our_order_id = $payment_data['notes']['order_id'];
} elseif (isset($order_data['notes']['order_id'])) {
    $our_order_id = $order_data['notes']['order_id'];
}

// Handle different webhook events
switch ($event) {
    case 'payment.authorized':
        // Payment authorized but not captured
        if ($our_order_id) {
            $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Authorized' WHERE id = ?");
            $stmt->bind_param("i", $our_order_id);
            $stmt->execute();
        }

        logWebhookEvent($event, $razorpay_payment_id, $our_order_id, 'Authorized', $payment_data);
        break;

    case 'payment.captured':
        // Payment successfully captured
        if ($our_order_id) {
            $stmt = $conn->prepare("
                UPDATE orders 
                SET payment_status = 'Completed', payment_id = ?, payment_date = NOW() 
                WHERE id = ?
            ");
            $stmt->bind_param("si", $razorpay_payment_id, $our_order_id);
            $stmt->execute();

            // Update payment record
            $stmt = $conn->prepare("
                UPDATE payments 
                SET status = 'Captured', gateway_response = ?, updated_at = NOW() 
                WHERE gateway_payment_id = ?
            ");
            $response_json = json_encode($payment_data);
            $stmt->bind_param("ss", $response_json, $razorpay_payment_id);
            $stmt->execute();
        }

        logWebhookEvent($event, $razorpay_payment_id, $our_order_id, 'Captured', $payment_data);
        break;

    case 'payment.failed':
        // Payment failed
        if ($our_order_id) {
            $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Failed' WHERE id = ?");
            $stmt->bind_param("i", $our_order_id);
            $stmt->execute();

            // Update payment record
            $stmt = $conn->prepare("
                UPDATE payments 
                SET status = 'Failed', gateway_response = ?, updated_at = NOW() 
                WHERE gateway_payment_id = ?
            ");
            $response_json = json_encode($payment_data);
            $stmt->bind_param("ss", $response_json, $razorpay_payment_id);
            $stmt->execute();
        }

        logWebhookEvent($event, $razorpay_payment_id, $our_order_id, 'Failed', $payment_data);
        break;

    case 'order.paid':
        // Order fully paid
        if ($our_order_id) {
            $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Completed' WHERE id = ?");
            $stmt->bind_param("i", $our_order_id);
            $stmt->execute();
        }

        logWebhookEvent($event, $razorpay_payment_id, $our_order_id, 'Paid', $order_data);
        break;

    case 'refund.created':
        // Refund created
        $refund_data = $data['payload']['refund']['entity'] ?? [];
        $refund_id = $refund_data['id'] ?? '';
        $refund_amount = $refund_data['amount'] ?? 0;

        if ($our_order_id && $refund_id) {
            // Insert refund record
            $stmt = $conn->prepare("
                INSERT INTO refunds (
                    payment_id, order_id, refund_amount, reason, status, 
                    gateway_refund_id, gateway_response, created_at
                ) VALUES (?, ?, ?, 'Webhook Refund', 'Processing', ?, ?, NOW())
            ");

            $refund_amount_decimal = $refund_amount / 100; // Convert paise to rupees
            $refund_response = json_encode($refund_data);

            $stmt->bind_param("sidss", $razorpay_payment_id, $our_order_id, $refund_amount_decimal, $refund_id, $refund_response);
            $stmt->execute();
        }

        logWebhookEvent($event, $razorpay_payment_id, $our_order_id, 'Refund Created', $refund_data);
        break;

    default:
        // Log unknown events
        logWebhookEvent($event, $razorpay_payment_id, $our_order_id, 'Unknown Event', $data);
        break;
}

// Send success response
http_response_code(200);
echo json_encode(['status' => 'success', 'event' => $event]);

// Optional: Send email notifications for important events
function sendPaymentNotification($event, $order_id, $payment_id)
{
    // Implement email notification logic here
    // You can use PHPMailer or similar library

    switch ($event) {
        case 'payment.captured':
            // Send payment success email to customer
            break;
        case 'payment.failed':
            // Send payment failure notification
            break;
        case 'refund.created':
            // Send refund notification
            break;
    }
}
