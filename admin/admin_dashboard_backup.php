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
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- AOS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
      color: white;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
    }

    .sidebar a:hover {
      background-color: #495057;
    }

    .profile-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid white;
    }

    .border-left-primary {
      border-left: 4px solid #007bff;
    }

    .border-left-success {
      border-left: 4px solid #28a745;
    }

    .border-left-warning {
      border-left: 4px solid #ffc107;
    }

    .border-left-info {
      border-left: 4px solid #17a2b8;
    }
  </style>
</head>

<body>

  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 sidebar d-flex flex-column p-4" data-aos="fade-right">
        <div class="text-center mb-4">
          <img src="../uploads/<?php echo htmlspecialchars($profile_img); ?>" class="profile-img mb-2" alt="Admin Image" />
          <h5><?php echo $_SESSION['admin_name']; ?></h5>
        </div>
        <nav>
          <a href="admin_dashboard.php">Dashboard</a>
          <a href="orders.php">Orders</a>
          <a href="menu_items.php">Menu Items</a>
          <a href="payments.php">Payments</a>
          <a href="customers.php">Customers</a>
          <a href="messages.php">Messages</a>
          <a href="logout.php" class="mt-auto text-danger">Logout</a>
        </nav>

      </div>

      <!-- Main Content -->
      <div class="col-md-9 p-4" data-aos="fade-up">
        <h2 class="mb-4">Admin Dashboard</h2>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="card shadow text-center p-3 border-left-primary">
              <h4><i class="fas fa-shopping-cart text-primary"></i> Total Orders</h4>
              <p class="display-6 text-primary"><?= $stats['total_orders'] ?></p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow text-center p-3 border-left-success">
              <h4><i class="fas fa-utensils text-success"></i> Menu Items</h4>
              <p class="display-6 text-success"><?= $stats['total_items'] ?></p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow text-center p-3 border-left-warning">
              <h4><i class="fas fa-users text-warning"></i> Total Users</h4>
              <p class="display-6 text-warning"><?= $stats['total_users'] ?></p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow text-center p-3 border-left-info">
              <h4><i class="fas fa-rupee-sign text-info"></i> Revenue</h4>
              <p class="display-6 text-info">₹<?= number_format($stats['total_revenue'], 0) ?></p>
            </div>
          </div>
        </div>

        <!-- Order Status Overview -->
        <div class="row g-4 mb-4">
          <div class="col-md-6">
            <div class="card shadow p-3">
              <h5 class="card-title"><i class="fas fa-chart-pie"></i> Order Status Overview</h5>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Status</th>
                      <th>Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($order_status as $status => $count): ?>
                      <tr>
                        <td>
                          <?php
                          $badge_color = '';
                          switch ($status) {
                            case 'Pending':
                              $badge_color = 'warning';
                              break;
                            case 'Confirmed':
                              $badge_color = 'info';
                              break;
                            case 'Preparing':
                              $badge_color = 'primary';
                              break;
                            case 'Ready':
                              $badge_color = 'success';
                              break;
                            case 'Out for Delivery':
                              $badge_color = 'dark';
                              break;
                            case 'Delivered':
                              $badge_color = 'success';
                              break;
                            case 'Cancelled':
                              $badge_color = 'danger';
                              break;
                          }
                          ?>
                          <span class="badge bg-<?= $badge_color ?>"><?= $status ?></span>
                        </td>
                        <td><?= $count ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="col-md-6">
            <div class="card shadow p-3">
              <h5 class="card-title"><i class="fas fa-clock"></i> Recent Orders</h5>
              <div class="table-responsive">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Order #</th>
                      <th>Customer</th>
                      <th>Total</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($order = $recent_orders->fetch_assoc()): ?>
                      <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
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
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
              <div class="text-center mt-2">
                <a href="orders.php" class="btn btn-sm btn-primary">View All Orders</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>

</html>