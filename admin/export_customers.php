<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Search parameters from the filter
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

// Build WHERE clause
$where_conditions = ['1=1'];
$params = [];
$param_types = '';

if ($search) {
    $where_conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $param_types .= 'sss';
}

if ($filter == 'active') {
    $where_conditions[] = "u.last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
} elseif ($filter == 'inactive') {
    $where_conditions[] = "(u.last_login < DATE_SUB(NOW(), INTERVAL 30 DAY) OR u.last_login IS NULL)";
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get customers for export
$query = "
    SELECT u.id, u.name, u.email, u.phone, u.address, u.created_at, u.last_login,
           COUNT(DISTINCT o.id) as total_orders,
           COALESCE(SUM(CASE WHEN o.payment_status = 'Completed' THEN o.total ELSE 0 END), 0) as total_spent,
           MAX(o.created_at) as last_order_date
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id
    $where_clause
    GROUP BY u.id
    ORDER BY u.created_at DESC
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
header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// CSV headers
fputcsv($output, [
    'Customer ID',
    'Name',
    'Email',
    'Phone',
    'Address',
    'Registration Date',
    'Last Login',
    'Total Orders',
    'Total Spent',
    'Last Order Date',
    'Status'
]);

// CSV data
while ($row = $result->fetch_assoc()) {
    $isActive = $row['last_login'] &&
        strtotime($row['last_login']) > strtotime('-30 days');

    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['phone'] ?? '',
        $row['address'] ?? '',
        $row['created_at'],
        $row['last_login'] ?? '',
        $row['total_orders'],
        $row['total_spent'],
        $row['last_order_date'] ?? '',
        $isActive ? 'Active' : 'Inactive'
    ]);
}

fclose($output);
exit();
