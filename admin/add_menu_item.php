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

if (isset($_POST['add'])) {
  $name        = htmlspecialchars(trim($_POST['name']));
  $main_category = htmlspecialchars(trim($_POST['main_category']));
  $sub_category  = htmlspecialchars(trim($_POST['sub_category']));
  $price       = floatval($_POST['price']);
  $description = htmlspecialchars(trim($_POST['description']));

  // Image upload
  $image       = $_FILES['image'];
  $target_dir  = "uploads/";
  $img_name    = basename($image['name']);
  $img_tmp     = $image['tmp_name'];
  $img_ext     = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
  $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

  if (!in_array($img_ext, $allowed_exts)) {
    $_SESSION['error'] = "Only JPG, JPEG, PNG, GIF files are allowed.";
    header("Location: add_menu_item.php");
    exit();
  }

  $new_img_name = uniqid("FOOD_", true) . '.' . $img_ext;
  $img_path = $target_dir . $new_img_name;

  if (!move_uploaded_file($img_tmp, "../" . $img_path)) {
    $_SESSION['error'] = "Image upload failed.";
    header("Location: add_menu_item.php");
    exit();
  }

  $stmt = $conn->prepare("INSERT INTO menu_items (name, main_category, sub_category, price, description, image) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssdss", $name, $main_category, $sub_category, $price, $description, $img_path);

  if ($stmt->execute()) {
    $_SESSION['success'] = "Menu item added successfully!";
    header("Location: menu_items.php");
    exit();
  } else {
    $_SESSION['error'] = "Failed to add item.";
    header("Location: add_menu_item.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Menu Item - HungerHub</title>
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

    .form-card {
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
          <h2><i class="fas fa-plus me-2"></i>Add New Menu Item</h2>
          <a href="menu_items.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Menu Items
          </a>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="card form-card">
              <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Menu Item Details</h5>
              </div>
              <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error'];
                                                                  unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">
                        <i class="fas fa-utensils me-1"></i>Item Name *
                      </label>
                      <input type="text" name="name" class="form-control" required placeholder="Enter item name">
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">
                        <i class="fas fa-rupee-sign me-1"></i>Price (₹) *
                      </label>
                      <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">
                        <i class="fas fa-leaf me-1"></i>Main Category *
                      </label>
                      <select name="main_category" class="form-select" required>
                        <option value="">-- Select Main Category --</option>
                        <option value="Veg">🥬 Veg</option>
                        <option value="Non-Veg">🍗 Non-Veg</option>
                      </select>
                    </div>

                    <div class="col-md-6 mb-3">
                      <label class="form-label">
                        <i class="fas fa-tags me-1"></i>Sub Category *
                      </label>
                      <select name="sub_category" class="form-select" required>
                        <option value="">-- Select Sub Category --</option>
                        <option value="Pizza">🍕 Pizza</option>
                        <option value="Biryani">🍚 Biryani</option>
                        <option value="Snacks">🍿 Snacks</option>
                        <option value="South Indian">🌶️ South Indian</option>
                        <option value="Chinese">🥢 Chinese</option>
                        <option value="Thali">🍽️ Thali</option>
                        <option value="Street Food">🌭 Street Food</option>
                        <option value="Desserts">🍰 Desserts</option>
                        <option value="Salads">🥗 Salads</option>
                        <option value="Breakfast">🍳 Breakfast</option>
                      </select>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">
                      <i class="fas fa-align-left me-1"></i>Description
                    </label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the item..."></textarea>
                  </div>

                  <div class="mb-4">
                    <label class="form-label">
                      <i class="fas fa-image me-1"></i>Item Image *
                    </label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <div class="form-text">Supported formats: JPG, JPEG, PNG, GIF</div>
                  </div>

                  <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="menu_items.php" class="btn btn-secondary me-md-2">
                      <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <button type="submit" name="add" class="btn btn-success">
                      <i class="fas fa-plus me-1"></i>Add Menu Item
                    </button>
                  </div>
                </form>
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