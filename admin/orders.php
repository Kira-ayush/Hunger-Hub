<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
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

  <?php if ($result->num_rows > 0): ?>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Items</th>
          <th>Total (₹)</th>
          <th>Ordered On</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['customer_name']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
          <td><?= htmlspecialchars($row['items']) ?></td>
          <td><?= number_format($row['total'], 2) ?></td>
          <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <div class="alert alert-info">No orders found.</div>
  <?php endif; ?>
</div>
</body>
</html>
