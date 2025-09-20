<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Fetch customers with their order statistics
$sql = "SELECT 
    u.id, u.name, u.email, u.phone, u.created_at,
    COUNT(o.id) as total_orders,
    COALESCE(SUM(o.total), 0) as total_spent,
    MAX(o.created_at) as last_order_date
FROM users u 
LEFT JOIN orders o ON u.id = o.user_id 
GROUP BY u.id 
ORDER BY u.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Management - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 data-aos="fade-right"><i class="fas fa-users"></i> Customer Management</h2>
            <a href="admin_dashboard.php" class="btn btn-secondary" data-aos="fade-left">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive" data-aos="zoom-in">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Customer Info</th>
                            <th>Contact</th>
                            <th>Registration Date</th>
                            <th>Total Orders</th>
                            <th>Total Spent (₹)</th>
                            <th>Last Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($customer = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($customer['name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($customer['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($customer['phone']) ?></td>
                                <td><?= date('M j, Y', strtotime($customer['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $customer['total_orders'] ?></span>
                                </td>
                                <td>₹<?= number_format($customer['total_spent'], 2) ?></td>
                                <td>
                                    <?php if ($customer['last_order_date']): ?>
                                        <?= date('M j, Y', strtotime($customer['last_order_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">No orders</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#customerModal<?= $customer['id'] ?>">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </td>
                            </tr>

                            <!-- Customer Details Modal -->
                            <div class="modal fade" id="customerModal<?= $customer['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-user"></i> Customer Details: <?= htmlspecialchars($customer['name']) ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Customer Info -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-info-circle"></i> Customer Information</h6>
                                                    <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
                                                    <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
                                                    <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
                                                    <p><strong>Member Since:</strong> <?= date('M j, Y', strtotime($customer['created_at'])) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-chart-bar"></i> Order Statistics</h6>
                                                    <p><strong>Total Orders:</strong> <?= $customer['total_orders'] ?></p>
                                                    <p><strong>Total Spent:</strong> ₹<?= number_format($customer['total_spent'], 2) ?></p>
                                                    <p><strong>Last Order:</strong>
                                                        <?= $customer['last_order_date'] ? date('M j, Y', strtotime($customer['last_order_date'])) : 'None' ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Recent Orders -->
                                            <h6><i class="fas fa-shopping-cart"></i> Recent Orders</h6>
                                            <?php
                                            $customer_orders = $conn->query("SELECT * FROM orders WHERE user_id = {$customer['id']} ORDER BY created_at DESC LIMIT 5");
                                            if ($customer_orders->num_rows > 0):
                                            ?>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Order #</th>
                                                                <th>Items</th>
                                                                <th>Total</th>
                                                                <th>Status</th>
                                                                <th>Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php while ($order = $customer_orders->fetch_assoc()): ?>
                                                                <tr>
                                                                    <td>#<?= $order['id'] ?></td>
                                                                    <td><?= htmlspecialchars(substr($order['items'], 0, 30)) ?>...</td>
                                                                    <td>₹<?= number_format($order['total'], 2) ?></td>
                                                                    <td>
                                                                        <?php
                                                                        $status_color = '';
                                                                        switch ($order['status'] ?? 'Pending') {
                                                                            case 'Pending':
                                                                                $status_color = 'warning';
                                                                                break;
                                                                            case 'Confirmed':
                                                                                $status_color = 'info';
                                                                                break;
                                                                            case 'Preparing':
                                                                                $status_color = 'primary';
                                                                                break;
                                                                            case 'Ready':
                                                                                $status_color = 'success';
                                                                                break;
                                                                            case 'Out for Delivery':
                                                                                $status_color = 'dark';
                                                                                break;
                                                                            case 'Delivered':
                                                                                $status_color = 'success';
                                                                                break;
                                                                            case 'Cancelled':
                                                                                $status_color = 'danger';
                                                                                break;
                                                                            default:
                                                                                $status_color = 'secondary';
                                                                        }
                                                                        ?>
                                                                        <span class="badge bg-<?= $status_color ?>"><?= htmlspecialchars($order['status'] ?? 'Pending') ?></span>
                                                                    </td>
                                                                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info">No orders found for this customer.</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" data-aos="fade-up">No customers found.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>