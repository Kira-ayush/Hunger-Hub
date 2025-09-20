<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

// Filter logic
$where = "1"; // default true
$filter_main = $_GET['main_category'] ?? '';
$filter_sub = $_GET['sub_category'] ?? '';

if (!empty($filter_main)) {
    $where .= " AND main_category = '" . $conn->real_escape_string($filter_main) . "'";
}
if (!empty($filter_sub)) {
    $where .= " AND sub_category = '" . $conn->real_escape_string($filter_sub) . "'";
}

$result = $conn->query("SELECT * FROM menu_items WHERE $where ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Menu Items</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 data-aos="fade-right">Manage Menu Items</h2>
    <a href="add_menu_item.php" class="btn btn-success" data-aos="fade-left">+ Add New Item</a>
  </div>

  <!-- Filter Form -->
  <form method="get" class="row g-3 mb-4" data-aos="fade-down">
    <div class="col-md-3">
      <select name="main_category" class="form-select">
        <option value="">All Main Categories</option>
        <option value="Veg" <?= $filter_main == 'Veg' ? 'selected' : '' ?>>Veg</option>
        <option value="Non-Veg" <?= $filter_main == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
      </select>
    </div>
    <div class="col-md-3">
      <select name="sub_category" class="form-select">
        <option value="">All Sub Categories</option>
        <?php
        $subs = ['Pizza', 'Biryani', 'Snacks', 'South Indian', 'Chinese', 'Thali', 'Street Food', 'Desserts', 'Salads', 'Breakfast'];
        foreach ($subs as $sub) {
          $sel = $filter_sub == $sub ? 'selected' : '';
          echo "<option value=\"$sub\" $sel>$sub</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary">Filter</button>
      <a href="menu_items.php" class="btn btn-secondary ms-2">Reset</a>
    </div>
  </form>

  <!-- Alerts -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <!-- Table -->
  <?php if ($result->num_rows > 0): ?>
    <div class="table-responsive" data-aos="zoom-in">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Main Category</th>
            <th>Sub Category</th>
            <th>Price (₹)</th>
            <th>Description</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><img src="../<?= htmlspecialchars($row['image']) ?>" width="60" height="60" style="object-fit:cover;" alt=""></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['main_category']) ?></td>
            <td><?= htmlspecialchars($row['sub_category']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
              <a href="edit_menu_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">Edit</a>
              <a href="delete_menu_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="alert alert-info" data-aos="fade-up">No menu items found.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
