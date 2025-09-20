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

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

// Search parameters
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

// Get total count
$count_query = "SELECT COUNT(*) as total FROM users u $where_clause";
if ($params) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_records = $conn->query($count_query)->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $per_page);

// Get customers with order statistics
$query = "
    SELECT u.*, 
           COUNT(DISTINCT o.id) as total_orders,
           COALESCE(SUM(CASE WHEN o.payment_status = 'Completed' THEN o.total ELSE 0 END), 0) as total_spent,
           MAX(o.created_at) as last_order_date
    FROM users u 
    LEFT JOIN orders o ON u.id = o.user_id
    $where_clause
    GROUP BY u.id
    ORDER BY u.created_at DESC 
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
$customers = $stmt->get_result();

// Get customer statistics
$stats_query = "
    SELECT 
        COUNT(*) as total_customers,
        COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_customers,
        COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as new_customers
    FROM users
";
$stats = $conn->query($stats_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

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

        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .customer-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
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
                    <a class="nav-link" href="payments.php">
                        <i class="fas fa-credit-card me-2"></i>Payments
                    </a>
                    <a class="nav-link active" href="customers.php">
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
                    <h2><i class="fas fa-users me-2"></i>Customer Management</h2>
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="exportCustomers()">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                    </div>
                </div>

                <!-- Customer Statistics -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h5><?= number_format($stats['total_customers']) ?></h5>
                                <small class="text-muted">Total Customers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                                <h5><?= number_format($stats['active_customers']) ?></h5>
                                <small class="text-muted">Active Customers (30 days)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card text-center">
                            <div class="card-body">
                                <i class="fas fa-user-plus fa-2x text-info mb-2"></i>
                                <h5><?= number_format($stats['new_customers']) ?></h5>
                                <small class="text-muted">New This Week</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Search & Filter</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Search Customers</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by name, email, or phone" value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Filter by Activity</label>
                                <select name="filter" class="form-select">
                                    <option value="">All Customers</option>
                                    <option value="active" <?= $filter == 'active' ? 'selected' : '' ?>>Active (30 days)</option>
                                    <option value="inactive" <?= $filter == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="customers.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Customer List</h5>
                        <span class="badge bg-primary"><?= number_format($total_records) ?> customers</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Contact</th>
                                        <th>Address</th>
                                        <th>Orders</th>
                                        <th>Total Spent</th>
                                        <th>Last Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($customer = $customers->fetch_assoc()): ?>
                                        <?php
                                        $isActive = $customer['last_login'] &&
                                            strtotime($customer['last_login']) > strtotime('-30 days');
                                        $initials = strtoupper(substr($customer['name'], 0, 1));
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="customer-avatar me-2"><?= $initials ?></div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($customer['name']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $customer['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($customer['email']) ?>
                                                    <?php if ($customer['phone']): ?>
                                                        <br><i class="fas fa-phone me-1"></i><?= htmlspecialchars($customer['phone']) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small><?= $customer['address'] ? htmlspecialchars($customer['address']) : 'Not provided' ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $customer['total_orders'] ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">₹<?= number_format($customer['total_spent'], 2) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($customer['last_order_date']): ?>
                                                    <small><?= date('M d, Y', strtotime($customer['last_order_date'])) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Never</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="customer-status status-<?= $isActive ? 'active' : 'inactive' ?>">
                                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary"
                                                        onclick="viewCustomer(<?= $customer['id'] ?>)"
                                                        title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info"
                                                        onclick="viewOrders(<?= $customer['id'] ?>)"
                                                        title="View Orders">
                                                        <i class="fas fa-shopping-bag"></i>
                                                    </button>
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
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="customerDetails">
                    <!-- Customer details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function viewCustomer(customerId) {
            fetch('get_customer_details.php?id=' + customerId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('customerDetails').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('customerModal')).show();
                })
                .catch(error => {
                    alert('Error loading customer details');
                    console.error('Error:', error);
                });
        }

        function viewOrders(customerId) {
            window.location.href = 'orders.php?customer_id=' + customerId;
        }

        function exportCustomers() {
            window.location.href = 'export_customers.php?' + new URLSearchParams(window.location.search);
        }
    </script>
</body>

</html>