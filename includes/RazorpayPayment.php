<?php

/**
 * Razorpay Payment Integration for HungerHub
 * 
 * This class handles Razorpay payment processing
 */

require_once 'payment_config.php';
require_once 'db.php';

class RazorpayPayment
{
    private $key_id;
    private $key_secret;
    private $webhook_secret;
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;

        $config = getPaymentConfig('razorpay');
        $this->key_id = $config['key_id'];
        $this->key_secret = $config['key_secret'];
        $this->webhook_secret = $config['webhook_secret'];
    }

    /**
     * Create a new Razorpay order
     */
    public function createOrder($amount, $currency, $receipt, $notes = [])
    {
        $url = 'https://api.razorpay.com/v1/orders';

        $data = [
            'amount' => $amount * 100, // Convert to paisa
            'currency' => $currency,
            'receipt' => $receipt,
            'notes' => $notes
        ];

        $response = $this->makeApiCall($url, 'POST', $data);

        if ($response && isset($response['id'])) {
            // Log the order creation
            $this->logPaymentEvent('order_created', [
                'razorpay_order_id' => $response['id'],
                'amount' => $amount,
                'currency' => $currency,
                'receipt' => $receipt
            ]);

            return $response;
        }

        return false;
    }

    /**
     * Verify payment signature
     */
    public function verifyPayment($razorpay_order_id, $razorpay_payment_id, $razorpay_signature)
    {
        $body = $razorpay_order_id . "|" . $razorpay_payment_id;
        $expected_signature = hash_hmac('sha256', $body, $this->key_secret);

        return hash_equals($expected_signature, $razorpay_signature);
    }

    /**
     * Capture a payment
     */
    public function capturePayment($payment_id, $amount, $currency = 'INR')
    {
        $url = "https://api.razorpay.com/v1/payments/{$payment_id}/capture";

        $data = [
            'amount' => $amount * 100, // Convert to paisa
            'currency' => $currency
        ];

        $response = $this->makeApiCall($url, 'POST', $data);

        if ($response && $response['status'] === 'captured') {
            $this->logPaymentEvent('payment_captured', [
                'payment_id' => $payment_id,
                'amount' => $amount,
                'currency' => $currency
            ]);

            return $response;
        }

        return false;
    }

    /**
     * Get payment details
     */
    public function getPayment($payment_id)
    {
        $url = "https://api.razorpay.com/v1/payments/{$payment_id}";
        return $this->makeApiCall($url, 'GET');
    }

    /**
     * Create a refund
     */
    public function createRefund($payment_id, $amount = null, $notes = [])
    {
        $url = "https://api.razorpay.com/v1/payments/{$payment_id}/refund";

        $data = ['notes' => $notes];
        if ($amount !== null) {
            $data['amount'] = $amount * 100; // Convert to paisa
        }

        $response = $this->makeApiCall($url, 'POST', $data);

        if ($response && isset($response['id'])) {
            $this->logPaymentEvent('refund_created', [
                'payment_id' => $payment_id,
                'refund_id' => $response['id'],
                'amount' => $amount
            ]);

            return $response;
        }

        return false;
    }

    /**
     * Process payment from frontend
     */
    public function processPayment($order_id, $payment_data)
    {
        try {
            // Verify payment signature
            if (!$this->verifyPayment(
                $payment_data['razorpay_order_id'],
                $payment_data['razorpay_payment_id'],
                $payment_data['razorpay_signature']
            )) {
                throw new Exception('Payment signature verification failed');
            }

            // Get payment details from Razorpay
            $payment_details = $this->getPayment($payment_data['razorpay_payment_id']);

            if (!$payment_details) {
                throw new Exception('Unable to fetch payment details');
            }

            // Update order in database
            $this->updateOrderPayment($order_id, $payment_details);

            // Store payment details in payments table
            $this->storePaymentDetails($order_id, $payment_details);

            return [
                'success' => true,
                'payment_id' => $payment_details['id'],
                'status' => $payment_details['status']
            ];
        } catch (Exception $e) {
            $this->logPaymentEvent('payment_error', [
                'order_id' => $order_id,
                'error' => $e->getMessage(),
                'payment_data' => $payment_data
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update order with payment information
     */
    private function updateOrderPayment($order_id, $payment_details)
    {
        $stmt = $this->conn->prepare("
            UPDATE orders SET 
                payment_method = 'Razorpay',
                payment_status = ?,
                payment_id = ?,
                payment_amount = ?,
                currency = ?,
                payment_date = NOW(),
                payment_gateway_response = ?,
                status = 'Confirmed'
            WHERE id = ?
        ");

        $payment_status = ($payment_details['status'] === 'captured') ? 'Completed' : 'Processing';
        $amount = $payment_details['amount'] / 100; // Convert from paisa
        $response_json = json_encode($payment_details);

        $stmt->bind_param(
            'ssdss',
            $payment_status,
            $payment_details['id'],
            $amount,
            $payment_details['currency'],
            $response_json,
            $order_id
        );

        return $stmt->execute();
    }

    /**
     * Store detailed payment information
     */
    private function storePaymentDetails($order_id, $payment_details)
    {
        // Get user_id from order
        $order_result = $this->conn->query("SELECT user_id, total FROM orders WHERE id = $order_id");
        $order = $order_result->fetch_assoc();

        if (!$order) {
            throw new Exception('Order not found');
        }

        $stmt = $this->conn->prepare("
            INSERT INTO payments (
                order_id, user_id, payment_gateway, gateway_payment_id, 
                gateway_order_id, amount, currency, status, 
                gateway_response, transaction_fee, net_amount
            ) VALUES (?, ?, 'Razorpay', ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $amount = $payment_details['amount'] / 100;
        $fee = calculatePaymentFee($amount, 'Razorpay');
        $net_amount = $amount - $fee;
        $status = ($payment_details['status'] === 'captured') ? 'Captured' : 'Authorized';
        $gateway_response = json_encode($payment_details);

        $stmt->bind_param(
            'iissdssdd',
            $order_id,
            $order['user_id'],
            $payment_details['id'],
            $payment_details['order_id'] ?? null,
            $amount,
            $payment_details['currency'],
            $status,
            $gateway_response,
            $fee,
            $net_amount
        );

        return $stmt->execute();
    }

    /**
     * Make API call to Razorpay
     */
    private function makeApiCall($url, $method = 'GET', $data = null)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERPWD => $this->key_id . ':' . $this->key_secret,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logPaymentEvent('api_error', [
                'url' => $url,
                'method' => $method,
                'error' => $error
            ]);
            return false;
        }

        if ($http_code >= 400) {
            $this->logPaymentEvent('api_error', [
                'url' => $url,
                'method' => $method,
                'http_code' => $http_code,
                'response' => $response
            ]);
            return false;
        }

        return json_decode($response, true);
    }

    /**
     * Log payment events
     */
    private function logPaymentEvent($event, $data)
    {
        logPaymentEvent("razorpay_{$event}", $data);

        // Also store in payment_logs table
        $stmt = $this->conn->prepare("
            INSERT INTO payment_logs (
                order_id, event_type, gateway, request_data, 
                response_data, ip_address, user_agent
            ) VALUES (?, ?, 'Razorpay', ?, ?, ?, ?)
        ");

        $order_id = $data['order_id'] ?? null;
        $event_type = ucfirst(str_replace('_', ' ', $event));
        $request_data = json_encode($data);
        $response_data = null;
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmt->bind_param(
            'isssss',
            $order_id,
            $event_type,
            $request_data,
            $response_data,
            $ip_address,
            $user_agent
        );

        $stmt->execute();
    }

    /**
     * Handle webhook from Razorpay
     */
    public function handleWebhook($payload, $signature)
    {
        // Verify webhook signature
        $expected_signature = hash_hmac('sha256', $payload, $this->webhook_secret);

        if (!hash_equals($expected_signature, $signature)) {
            $this->logPaymentEvent('webhook_signature_failed', [
                'payload' => $payload,
                'signature' => $signature
            ]);
            return false;
        }

        $event = json_decode($payload, true);

        if (!$event) {
            return false;
        }

        $this->logPaymentEvent('webhook_received', [
            'event' => $event['event'],
            'payment_id' => $event['payload']['payment']['entity']['id'] ?? null
        ]);

        // Process different webhook events
        switch ($event['event']) {
            case 'payment.captured':
                return $this->handlePaymentCaptured($event['payload']['payment']['entity']);

            case 'payment.failed':
                return $this->handlePaymentFailed($event['payload']['payment']['entity']);

            case 'refund.processed':
                return $this->handleRefundProcessed($event['payload']['refund']['entity']);

            default:
                // Log unknown events
                $this->logPaymentEvent('webhook_unknown_event', $event);
                return true;
        }
    }

    /**
     * Handle payment captured webhook
     */
    private function handlePaymentCaptured($payment)
    {
        $payment_id = $payment['id'];

        // Update payment status in database
        $stmt = $this->conn->prepare("
            UPDATE payments SET status = 'Captured', updated_at = NOW() 
            WHERE gateway_payment_id = ?
        ");
        $stmt->bind_param('s', $payment_id);
        $stmt->execute();

        // Update order status
        $stmt = $this->conn->prepare("
            UPDATE orders SET payment_status = 'Completed', status = 'Confirmed'
            WHERE payment_id = ?
        ");
        $stmt->bind_param('s', $payment_id);
        $stmt->execute();

        return true;
    }

    /**
     * Handle payment failed webhook
     */
    private function handlePaymentFailed($payment)
    {
        $payment_id = $payment['id'];

        // Update payment status in database
        $stmt = $this->conn->prepare("
            UPDATE payments SET status = 'Failed', updated_at = NOW() 
            WHERE gateway_payment_id = ?
        ");
        $stmt->bind_param('s', $payment_id);
        $stmt->execute();

        // Update order status
        $stmt = $this->conn->prepare("
            UPDATE orders SET payment_status = 'Failed', status = 'Payment Failed'
            WHERE payment_id = ?
        ");
        $stmt->bind_param('s', $payment_id);
        $stmt->execute();

        return true;
    }

    /**
     * Handle refund processed webhook
     */
    private function handleRefundProcessed($refund)
    {
        $payment_id = $refund['payment_id'];
        $refund_id = $refund['id'];

        // Update refund status in database
        $stmt = $this->conn->prepare("
            UPDATE refunds SET status = 'Completed', gateway_refund_id = ?, updated_at = NOW()
            WHERE payment_id = (SELECT id FROM payments WHERE gateway_payment_id = ?)
        ");
        $stmt->bind_param('ss', $refund_id, $payment_id);
        $stmt->execute();

        return true;
    }
}
