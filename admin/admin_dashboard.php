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
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$profile_img = $admin['profile_pic'] ?? 'default.png';

// Fetch real statistics
$stats = [];

// Total Orders
$result = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['total_orders'];

// Total Menu Items
$result = $conn->query("SELECT COUNT(*) as total_items FROM menu_items");
$stats['total_items'] = $result->fetch_assoc()['total_items'];

// Total Users
$result = $conn->query("SELECT COUNT(*) as total_users FROM users");
$stats['total_users'] = $result->fetch_assoc()['total_users'];

// Total Revenue
$result = $conn->query("SELECT SUM(total) as total_revenue FROM orders WHERE status != 'Cancelled'");
$stats['total_revenue'] = $result->fetch_assoc()['total_revenue'] ?? 0;

// Recent Orders
$recent_orders = $conn->query("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");

// Order Status Distribution
$status_result = $conn->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$order_status = [];
while ($row = $status_result->fetch_assoc()) {
  $order_status[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - HungerHub</title>
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

    .stat-card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
    }

    .profile-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .table-responsive {
      border-radius: 10px;
      overflow: hidden;
    }

    .order-status {
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
    }

    .status-confirmed {
      background: #d4edda;
      color: #155724;
    }

    .status-preparing {
      background: #d1ecf1;
      color: #0c5460;
    }

    .status-ready {
      background: #e2e3e5;
      color: #383d41;
    }

    .status-delivered {
      background: #d4edda;
      color: #155724;
    }

    .status-cancelled {
      background: #f8d7da;
      color: #721c24;
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
          <a class="nav-link active" href="admin_dashboard.php">
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
          <h2><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
          <div class="text-muted">
            <i class="fas fa-calendar me-1"></i><?= date('M d, Y') ?>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card stat-card text-center">
              <div class="card-body">
                <i class="fas fa-shopping-bag fa-2x text-primary mb-2"></i>
                <h5><?= number_format($stats['total_orders']) ?></h5>
                <small class="text-muted">Total Orders</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stat-card text-center">
              <div class="card-body">
                <i class="fas fa-utensils fa-2x text-success mb-2"></i>
                <h5><?= number_format($stats['total_items']) ?></h5>
                <small class="text-muted">Menu Items</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stat-card text-center">
              <div class="card-body">
                <i class="fas fa-users fa-2x text-warning mb-2"></i>
                <h5><?= number_format($stats['total_users']) ?></h5>
                <small class="text-muted">Total Users</small>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stat-card text-center">
              <div class="card-body">
                <i class="fas fa-money-bill-wave fa-2x text-info mb-2"></i>
                <h5>₹<?= number_format($stats['total_revenue'], 2) ?></h5>
                <small class="text-muted">Total Revenue</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Orders</h5>
            <a href="orders.php" class="btn btn-sm btn-primary">View All Orders</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($order = $recent_orders->fetch_assoc()): ?>
                    <tr>
                      <td>#<?= $order['id'] ?></td>
                      <td><?= htmlspecialchars($order['user_name'] ?? $order['customer_name'] ?? 'Guest') ?></td>
                      <td>₹<?= number_format($order['total'], 2) ?></td>
                      <td>
                        <span class="order-status status-<?= strtolower($order['status']) ?>">
                          <?= $order['status'] ?>
                        </span>
                      </td>
                      <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Order Status Chart -->
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Order Status Distribution</h5>
              </div>
              <div class="card-body">
                <?php foreach ($order_status as $status => $count): ?>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="order-status status-<?= strtolower($status) ?>">
                      <?= $status ?>
                    </span>
                    <strong><?= $count ?></strong>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Actions</h5>
              </div>
              <div class="card-body">
                <div class="d-grid gap-2">
                  <a href="add_menu_item.php" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Add Menu Item
                  </a>
                  <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>Manage Orders
                  </a>
                  <a href="customers.php" class="btn btn-info">
                    <i class="fas fa-users me-2"></i>View Customers
                  </a>
                  <a href="payments.php" class="btn btn-warning">
                    <i class="fas fa-credit-card me-2"></i>Payment Reports
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>