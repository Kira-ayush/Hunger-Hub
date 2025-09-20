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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Items Management - HungerHub</title>
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

    .menu-item-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 8px;
    }

    .table-responsive {
      border-radius: 10px;
      overflow: hidden;
    }

    .filter-card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
          <a class="nav-link active" href="menu_items.php">
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
          <h2><i class="fas fa-utensils me-2"></i>Menu Items Management</h2>
          <a href="add_menu_item.php" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Add New Item
          </a>
        </div>

        <!-- Filter Card -->
        <div class="card filter-card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Menu Items</h5>
          </div>
          <div class="card-body">
            <form method="get" class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Main Category</label>
                <select name="main_category" class="form-select">
                  <option value="">All Main Categories</option>
                  <option value="Veg" <?= $filter_main == 'Veg' ? 'selected' : '' ?>>Veg</option>
                  <option value="Non-Veg" <?= $filter_main == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Sub Category</label>
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
              <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                  <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="menu_items.php" class="btn btn-secondary">
                  <i class="fas fa-refresh me-1"></i>Reset
                </a>
              </div>
            </form>
          </div>
        </div>

        <!-- Alerts -->
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

        <!-- Menu Items Table -->
        <?php if ($result->num_rows > 0): ?>
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="fas fa-list me-2"></i>Menu Items</h5>
              <span class="badge bg-primary"><?= $result->num_rows ?> Items Found</span>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Image</th>
                      <th>Name</th>
                      <th>Main Category</th>
                      <th>Sub Category</th>
                      <th>Price</th>
                      <th>Description</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;
                    while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><strong><?= $i++ ?></strong></td>
                        <td>
                          <img src="../<?= htmlspecialchars($row['image']) ?>" class="menu-item-img" alt="<?= htmlspecialchars($row['name']) ?>">
                        </td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td>
                          <span class="badge bg-<?= $row['main_category'] == 'Veg' ? 'success' : 'danger' ?>">
                            <?= htmlspecialchars($row['main_category']) ?>
                          </span>
                        </td>
                        <td><?= htmlspecialchars($row['sub_category']) ?></td>
                        <td><strong>₹<?= number_format($row['price'], 2) ?></strong></td>
                        <td><small><?= htmlspecialchars($row['description']) ?></small></td>
                        <td>
                          <div class="btn-group" role="group">
                            <a href="edit_menu_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                              <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_menu_item.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">
                              <i class="fas fa-trash"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php else: ?>
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">No menu items found</h5>
              <p class="text-muted">Start by adding your first menu item.</p>
              <a href="add_menu_item.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add First Item
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>