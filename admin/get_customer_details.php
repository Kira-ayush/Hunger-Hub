<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}
require '../db.php';

$customer_id = intval($_GET['id'] ?? 0);

if (!$customer_id) {
    http_response_code(400);
    exit('Invalid customer ID');
}

// Get customer details
$stmt = $conn->prepare("
    SELECT u.*, 
           COUNT(DISTINCT o.id) as total_orders,
           COALESCE(SUM(CASE WHEN o.payment_status = 'Completed' THEN o.total ELSE 0 END), 0) as total_spent,
           MAX(o.created_at) as last_order_date,
           MIN(o.created_at) as first_order_date
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.id = ?
    GROUP BY u.id
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

if (!$customer) {
    http_response_code(404);
    exit('Customer not found');
}

// Get recent orders
$stmt = $conn->prepare("
    SELECT o.*, 
           GROUP_CONCAT(
               CONCAT(oi.quantity, 'x ', mi.name) 
               SEPARATOR ', '
           ) as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu_items mi ON oi.menu_item_id = mi.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$recent_orders = $stmt->get_result();

// Calculate customer metrics
$isActive = $customer['last_login'] &&
    strtotime($customer['last_login']) > strtotime('-30 days');

$customerSince = date('M Y', strtotime($customer['created_at']));
$averageOrderValue = $customer['total_orders'] > 0 ?
    $customer['total_spent'] / $customer['total_orders'] : 0;
?>

<div class="row">
    <div class="col-md-4">
        <div class="text-center mb-4">
            <div class="customer-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                <?= strtoupper(substr($customer['name'], 0, 1)) ?>
            </div>
            <h5><?= htmlspecialchars($customer['name']) ?></h5>
            <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?>">
                <?= $isActive ? 'Active' : 'Inactive' ?>
            </span>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Customer Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Email:</strong><br>
                    <small><?= htmlspecialchars($customer['email']) ?></small>
                </div>
                <?php if ($customer['phone']): ?>
                    <div class="mb-2">
                        <strong>Phone:</strong><br>
                        <small><?= htmlspecialchars($customer['phone']) ?></small>
                    </div>
                <?php endif; ?>
                <?php if ($customer['address']): ?>
                    <div class="mb-2">
                        <strong>Address:</strong><br>
                        <small><?= htmlspecialchars($customer['address']) ?></small>
                    </div>
                <?php endif; ?>
                <div class="mb-2">
                    <strong>Customer Since:</strong><br>
                    <small><?= $customerSince ?></small>
                </div>
                <?php if ($customer['last_login']): ?>
                    <div class="mb-2">
                        <strong>Last Login:</strong><br>
                        <small><?= date('M d, Y H:i', strtotime($customer['last_login'])) ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Order Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary"><?= $customer['total_orders'] ?></h4>
                        <small class="text-muted">Total Orders</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-success">₹<?= number_format($customer['total_spent'], 2) ?></h4>
                        <small class="text-muted">Total Spent</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info">₹<?= number_format($averageOrderValue, 2) ?></h4>
                        <small class="text-muted">Avg Order Value</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Orders</h6>
            </div>
            <div class="card-body">
                <?php if ($recent_orders->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td>
                                            <small><?= htmlspecialchars($order['items'] ?? 'No items') ?></small>
                                        </td>
                                        <td>₹<?= number_format($order['total'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?=
                                                                    $order['status'] == 'Delivered' ? 'success' : ($order['status'] == 'Cancelled' ? 'danger' : 'warning')
                                                                    ?>">
                                                <?= $order['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= date('M d, Y', strtotime($order['created_at'])) ?></small>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No orders found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .customer-avatar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        border-radius: 50%;
    }
</style>