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

// Handle status update
if (isset($_POST['update_status'])) {
  $order_id = intval($_POST['order_id']);
  $new_status = $_POST['status'];
  $admin_notes = htmlspecialchars(trim($_POST['admin_notes']));

  $stmt = $conn->prepare("UPDATE orders SET status = ?, admin_notes = ? WHERE id = ?");
  $stmt->bind_param("ssi", $new_status, $admin_notes, $order_id);

  if ($stmt->execute()) {
    $_SESSION['success'] = "Order status updated successfully!";
  } else {
    $_SESSION['error'] = "Failed to update order status.";
  }
  header("Location: orders.php");
  exit();
}

$result = $conn->query("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders Management - HungerHub</title>
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
      background: #d1ecf1;
      color: #0c5460;
    }

    .status-preparing {
      background: #cce5ff;
      color: #004085;
    }

    .status-ready {
      background: #d4edda;
      color: #155724;
    }

    .status-delivered {
      background: #d4edda;
      color: #155724;
    }

    .status-cancelled {
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
          <a class="nav-link active" href="orders.php">
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
          <h2><i class="fas fa-shopping-bag me-2"></i>Orders Management</h2>
          <div class="text-muted">
            <i class="fas fa-calendar me-1"></i><?= date('M d, Y') ?>
          </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success'];
                                                    unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error'];
                                                          unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Orders</h5>
              <span class="badge bg-primary"><?= $result->num_rows ?> Total Orders</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Customer</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Ordered On</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;
                    while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><strong>#<?= $row['id'] ?></strong></td>
                        <td>
                          <div>
                            <strong><?= htmlspecialchars($row['customer_name']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($row['user_email'] ?? 'N/A') ?></small>
                          </div>
                        </td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><small><?= nl2br(htmlspecialchars($row['address'])) ?></small></td>
                        <td><small><?= htmlspecialchars($row['items']) ?></small></td>
                        <td><strong>₹<?= number_format($row['total'], 2) ?></strong></td>
                        <td>
                          <span class="order-status status-<?= strtolower(str_replace(' ', '', $row['status'] ?? 'pending')) ?>">
                            <?= htmlspecialchars($row['status'] ?? 'Pending') ?>
                          </span>
                        </td>
                        <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
                        <td>
                          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal<?= $row['id'] ?>">
                            <i class="fas fa-edit me-1"></i>Update
                          </button>
                        </td>
                      </tr>

                      <!-- Status Update Modal -->
                      <div class="modal fade" id="statusModal<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>Update Order #<?= $row['id'] ?>
                              </h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="post">
                              <div class="modal-body">
                                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">

                                <div class="mb-3">
                                  <label class="form-label">Order Status</label>
                                  <select name="status" class="form-select" required>
                                    <option value="Pending" <?= ($row['status'] ?? 'Pending') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Confirmed" <?= ($row['status'] ?? '') == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Preparing" <?= ($row['status'] ?? '') == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                                    <option value="Ready" <?= ($row['status'] ?? '') == 'Ready' ? 'selected' : '' ?>>Ready</option>
                                    <option value="Out for Delivery" <?= ($row['status'] ?? '') == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                                    <option value="Delivered" <?= ($row['status'] ?? '') == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="Cancelled" <?= ($row['status'] ?? '') == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                  </select>
                                </div>

                                <div class="mb-3">
                                  <label class="form-label">Admin Notes</label>
                                  <textarea name="admin_notes" class="form-control" rows="3" placeholder="Add any notes about this order..."><?= htmlspecialchars($row['admin_notes'] ?? '') ?></textarea>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                  <i class="fas fa-save me-1"></i>Update Status
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">No orders found</h5>
              <p class="text-muted">Orders from customers will appear here.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>