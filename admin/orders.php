<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit();
}
require '../db.php';

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
<html>

<head>
  <meta charset="UTF-8">
  <title>All Orders - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4">📦 Customer Orders</h2>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success"><?php echo $_SESSION['success'];
                                        unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                      unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Items</th>
              <th>Total (₹)</th>
              <th>Status</th>
              <th>Ordered On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1;
            while ($row = $result->fetch_assoc()):
              $status_color = '';
              switch ($row['status'] ?? 'Pending') {
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
              <tr>
                <td><?= $i++ ?></td>
                <td>
                  <?= htmlspecialchars($row['customer_name']) ?><br>
                  <small class="text-muted"><?= htmlspecialchars($row['user_email'] ?? 'N/A') ?></small>
                </td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
                <td><?= htmlspecialchars($row['items']) ?></td>
                <td><?= number_format($row['total'], 2) ?></td>
                <td>
                  <span class="badge bg-<?= $status_color ?>"><?= htmlspecialchars($row['status'] ?? 'Pending') ?></span>
                </td>
                <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
                <td>
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal<?= $row['id'] ?>">
                    Update Status
                  </button>
                </td>
              </tr>

              <!-- Status Update Modal -->
              <div class="modal fade" id="statusModal<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Update Order #<?= $row['id'] ?></h5>
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
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info">No orders found.</div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>