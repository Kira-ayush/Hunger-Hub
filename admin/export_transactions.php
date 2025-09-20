<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Filter parameters
$payment_method = $_GET['payment_method'] ?? '';
$payment_status = $_GET['payment_status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_types = '';

if ($payment_method) {
    $where_conditions[] = "o.payment_method = ?";
    $params[] = $payment_method;
    $param_types .= 's';
}

if ($payment_status) {
    $where_conditions[] = "o.payment_status = ?";
    $params[] = $payment_status;
    $param_types .= 's';
}

if ($date_from) {
    $where_conditions[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
    $param_types .= 's';
}

if ($date_to) {
    $where_conditions[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
    $param_types .= 's';
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get transactions for export
$query = "
    SELECT o.id, o.total, o.payment_method, o.payment_status, o.created_at,
           u.name as customer_name, u.email as customer_email,
           p.gateway_payment_id as transaction_id, p.gateway_response
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN payments p ON o.id = p.order_id
    $where_clause
    ORDER BY o.created_at DESC
";

if ($params) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="transactions_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// CSV headers
fputcsv($output, [
    'Order ID',
    'Customer Name',
    'Customer Email',
    'Amount',
    'Payment Method',
    'Payment Status',
    'Transaction ID',
    'Order Date',
    'Payment Date'
]);

// CSV data
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['customer_name'] ?? 'Guest',
        $row['customer_email'] ?? '',
        $row['total'],
        $row['payment_method'],
        $row['payment_status'],
        $row['transaction_id'] ?? '',
        $row['created_at'],
        $row['created_at']
    ]);
}

fclose($output);
exit();
