<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Fetch profile image
$stmt = $conn->prepare("SELECT profile_pic FROM admins WHERE id = ?");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$result_admin = $stmt->get_result();
$admin = $result_admin->fetch_assoc();
$profile_img = $admin['profile_pic'] ?? 'default.png';

// Handle refund processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_refund'])) {
    $order_id = intval($_POST['order_id']);
    $refund_amount = floatval($_POST['refund_amount']);
    $refund_reason = htmlspecialchars(trim($_POST['refund_reason']));

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert refund record
        $stmt = $conn->prepare("INSERT INTO refunds (order_id, amount, reason, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())");
        $stmt->bind_param("ids", $order_id, $refund_amount, $refund_reason);
        $stmt->execute();

        // Update order payment status
        $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Refunded' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success'] = "Refund processed successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error processing refund: " . $e->getMessage();
    }

    header("Location: payments.php");
    exit();
}

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

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

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM orders o $where_clause";
if ($params) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_records = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $per_page);

// Get transactions with pagination
$query = "
    SELECT o.*, u.name as customer_name, u.email as customer_email,
           p.gateway_payment_id as transaction_id, p.gateway_response, p.created_at as payment_created_at
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN payments p ON o.id = p.order_id
    $where_clause
    ORDER BY o.created_at DESC 
    LIMIT ? OFFSET ?
";

$params[] = $per_page;
$params[] = $offset;
$param_types .= 'ii';

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$transactions = $stmt->get_result();

// Get payment statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN payment_status = 'Completed' THEN total ELSE 0 END) as total_revenue,
        SUM(CASE WHEN payment_status = 'Pending' THEN total ELSE 0 END) as pending_amount,
        SUM(CASE WHEN payment_status = 'Failed' THEN total ELSE 0 END) as failed_amount,
        COUNT(CASE WHEN payment_method = 'COD' THEN 1 END) as cod_count,
        COUNT(CASE WHEN payment_method = 'Razorpay' THEN 1 END) as razorpay_count,
        COUNT(CASE WHEN payment_method = 'PayPal' THEN 1 END) as paypal_count
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";
$stats = $conn->query($stats_query)->fetch_assoc();

// Get recent refunds
$refunds_query = "
    SELECT r.*, o.id as order_id, u.name as customer_name 
    FROM refunds r 
    JOIN orders o ON r.order_id = o.id 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY r.created_at DESC 
    LIMIT 10
";
$recent_refunds = $conn->query($refunds_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.min.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .profile-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .payment-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .status-refunded {
            background: #d1ecf1;
            color: #0c5460;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <div class="text-center mb-4">
                    <img src="../uploads/<?php echo htmlspecialchars($profile_img); ?>" class="profile-img mb-2" alt="Admin Image" />
                    <h6 class="text-white"><?php echo $_SESSION['admin_name']; ?></h6>
                </div>

                <h4 class="text-white mb-4">
                    <i class="fas fa-utensils me-2"></i>HungerHub Admin
                </h4>

                <nav class="nav flex-column">
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-bag me-2"></i>Orders
                    </a>
                    <a class="nav-link" href="menu_items.php">
                        <i class="fas fa-utensils me-2"></i>Menu Items
                    </a>
                    <a class="nav-link active" href="payments.php">
                        <i class="fas fa-credit-card me-2"></i>Payments
                    </a>
                    <a class="nav-link" href="customers.php">
                        <i class="fas fa-users me-2"></i>Customers
                    </a>
                    <a class="nav-link" href="messages.php">
                        <i class="fas fa-envelope me-2"></i>Messages
                    </a>
                    <hr class="text-white-50">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-credit-card me-2"></i>Payment Management</h2>
                    <div class="btn-group">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#analyticsModal">
                            <i class="fas fa-chart-bar me-2"></i>Analytics
                        </button>
                        <button class="btn btn-success" onclick="exportTransactions()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>

                <!-- Payment Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h5>₹<?= number_format($stats['total_revenue'], 2) ?></h5>
                                <small class="text-muted">Total Revenue (30 days)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                                <h5><?= number_format($stats['total_transactions']) ?></h5>
                                <small class="text-muted">Total Transactions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h5>₹<?= number_format($stats['pending_amount'], 2) ?></h5>
                                <small class="text-muted">Pending Amount</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                <h5>₹<?= number_format($stats['failed_amount'], 2) ?></h5>
                                <small class="text-muted">Failed Amount</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Transactions</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="">All Methods</option>
                                    <option value="COD" <?= $payment_method == 'COD' ? 'selected' : '' ?>>Cash on Delivery</option>
                                    <option value="Razorpay" <?= $payment_method == 'Razorpay' ? 'selected' : '' ?>>Razorpay</option>
                                    <option value="PayPal" <?= $payment_method == 'PayPal' ? 'selected' : '' ?>>PayPal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Pending" <?= $payment_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Completed" <?= $payment_status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Failed" <?= $payment_status == 'Failed' ? 'selected' : '' ?>>Failed</option>
                                    <option value="Refunded" <?= $payment_status == 'Refunded' ? 'selected' : '' ?>>Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="payments.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Transaction History</h5>
                        <span class="badge bg-primary"><?= number_format($total_records) ?> records</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $transactions->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#<?= $row['id'] ?></span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($row['customer_name'] ?? 'Guest') ?></strong>
                                                    <br><small class="text-muted"><?= htmlspecialchars($row['customer_email'] ?? '') ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold">₹<?= number_format($row['total'], 2) ?></span>
                                            </td>
                                            <td>
                                                <i class="fas fa-<?=
                                                                    $row['payment_method'] == 'COD' ? 'money-bill' : ($row['payment_method'] == 'Razorpay' ? 'credit-card' : 'paypal')
                                                                    ?> me-1"></i>
                                                <?= $row['payment_method'] ?>
                                            </td>
                                            <td>
                                                <span class="payment-status status-<?= strtolower($row['payment_status']) ?>">
                                                    <?= $row['payment_status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= $row['transaction_id'] ? htmlspecialchars($row['transaction_id']) : '-' ?></small>
                                            </td>
                                            <td>
                                                <small><?= date('M d, Y H:i', strtotime($row['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary"
                                                        onclick="viewTransaction(<?= $row['id'] ?>)"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if ($row['payment_status'] == 'Completed'): ?>
                                                        <button class="btn btn-outline-warning"
                                                            onclick="processRefund(<?= $row['id'] ?>, '<?= htmlspecialchars($row['customer_name']) ?>', <?= $row['total'] ?>)"
                                                            title="Process Refund">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer">
                            <nav>
                                <ul class="pagination pagination-sm justify-content-center mb-0">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Refunds -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-undo me-2"></i>Recent Refunds</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_refunds->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($refund = $recent_refunds->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?= $refund['order_id'] ?></td>
                                                <td><?= htmlspecialchars($refund['customer_name']) ?></td>
                                                <td>₹<?= number_format($refund['amount'], 2) ?></td>
                                                <td><?= htmlspecialchars($refund['reason']) ?></td>
                                                <td><span class="badge bg-warning"><?= $refund['status'] ?></span></td>
                                                <td><?= date('M d, Y', strtotime($refund['created_at'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">No refunds processed yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="refund_order_id">
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" id="refund_customer" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Refund Amount</label>
                            <input type="number" step="0.01" class="form-control" name="refund_amount"
                                id="refund_amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Refund</label>
                            <textarea class="form-control" name="refund_reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="process_refund" class="btn btn-warning">Process Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Analytics Modal -->
    <div class="modal fade" id="analyticsModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Analytics</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Methods Distribution</h6>
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>Revenue Trend (Last 7 Days)</h6>
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function processRefund(orderId, customerName, amount) {
            document.getElementById('refund_order_id').value = orderId;
            document.getElementById('refund_customer').value = customerName;
            document.getElementById('refund_amount').value = amount;
            new bootstrap.Modal(document.getElementById('refundModal')).show();
        }

        function viewTransaction(orderId) {
            // Implementation for viewing transaction details
            alert('Transaction details for Order #' + orderId);
        }

        function exportTransactions() {
            window.location.href = 'export_transactions.php?' + new URLSearchParams(window.location.search);
        }

        // Initialize charts when analytics modal is shown
        document.getElementById('analyticsModal').addEventListener('shown.bs.modal', function() {
            // Payment Methods Chart
            const ctx1 = document.getElementById('paymentMethodChart').getContext('2d');
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['COD', 'Razorpay', 'PayPal'],
                    datasets: [{
                        data: [<?= $stats['cod_count'] ?>, <?= $stats['razorpay_count'] ?>, <?= $stats['paypal_count'] ?>],
                        backgroundColor: ['#ff6384', '#36a2eb', '#4bc0c0']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Revenue Chart (placeholder data)
            const ctx2 = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                    datasets: [{
                        label: 'Revenue',
                        data: [1200, 1900, 3000, 5000, 2000, 3000, 4500],
                        borderColor: '#36a2eb',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>