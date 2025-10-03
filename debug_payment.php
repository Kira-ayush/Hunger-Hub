<?php
session_start();
require 'db.php';

echo "<h2>Payment Debug Information</h2>";

// Check session data
echo "<h3>Session Data:</h3>";
echo "<p><strong>pending_order_id:</strong> " . ($_SESSION['pending_order_id'] ?? 'Not set') . "</p>";
echo "<p><strong>pending_amount:</strong> " . ($_SESSION['pending_amount'] ?? 'Not set') . "</p>";

// Check if order exists
if (isset($_SESSION['pending_order_id'])) {
    $order_id = $_SESSION['pending_order_id'];

    echo "<h3>Order Verification:</h3>";
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo "<p>✅ Order found in database</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        foreach ($order as $key => $value) {
            echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Order NOT found in database</p>";
        echo "<p>This explains the foreign key constraint error!</p>";
    }
}

// Show recent orders
echo "<h3>Recent Orders:</h3>";
$recent_orders = $conn->query("SELECT id, payment_status, created_at FROM orders ORDER BY id DESC LIMIT 5");
if ($recent_orders) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Payment Status</th><th>Created</th></tr>";
    while ($row = $recent_orders->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['payment_status']}</td><td>{$row['created_at']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found</p>";
}

echo "<hr>";
echo "<a href='setup_test_session.php'>Setup Test Session</a> | ";
echo "<a href='checkout.php'>Go to Checkout</a> | ";
echo "<a href='payment_upi.php'>Go to UPI Payment</a>";
