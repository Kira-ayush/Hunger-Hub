<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

// Check if payment data is received
if (!$_POST || !isset($_POST['order_id']) || !isset($_POST['transaction_id'])) {
    $_SESSION['error'] = "Invalid payment data.";
    header("Location: menu.php");
    exit();
}

$order_id = $_POST['order_id'];
$transaction_id = $_POST['transaction_id'];
$payer_email = $_POST['payer_email'] ?? '';
$amount_usd = $_POST['amount'] ?? 0;

// Verify that the order belongs to the current user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order not found.";
    header("Location: menu.php");
    exit();
}

// Update order with PayPal payment information
$stmt = $conn->prepare("
    UPDATE orders 
    SET payment_status = 'Completed', 
        payment_id = ?, 
        payment_details = ?
    WHERE id = ?
");

$payment_details = json_encode([
    'transaction_id' => $transaction_id,
    'payer_email' => $payer_email,
    'amount_usd' => $amount_usd,
    'payment_method' => 'PayPal',
    'payment_date' => date('Y-m-d H:i:s')
]);

$stmt->bind_param("ssi", $transaction_id, $payment_details, $order_id);

if ($stmt->execute()) {
    // Log payment in payments table
    $stmt = $conn->prepare("
        INSERT INTO payments (
            order_id, user_id, payment_method, payment_id, 
            amount, currency, status, payment_details, created_at
        ) VALUES (?, ?, 'PayPal', ?, ?, 'USD', 'Completed', ?, NOW())
    ");

    $stmt->bind_param("iisds", $order_id, $_SESSION['user_id'], $transaction_id, $amount_usd, $payment_details);
    $stmt->execute();

    // Clear session data
    unset($_SESSION['cart']);
    unset($_SESSION['pending_order_id']);
    unset($_SESSION['pending_amount']);

    $_SESSION['success'] = "Payment successful! Your order has been confirmed.";
    $_SESSION['payment_method'] = "PayPal";
    $_SESSION['transaction_id'] = $transaction_id;

    header("Location: order_success.php");
} else {
    $_SESSION['error'] = "Failed to update payment status. Please contact support.";
    header("Location: payment_failed.php");
}
exit();
