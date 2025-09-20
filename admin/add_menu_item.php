<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

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
  <title>Add Menu Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="col-md-8 mx-auto" data-aos="fade-up">
    <div class="card shadow p-4">
      <h3 class="mb-4 text-center">Add Menu Item</h3>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Item Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
        <label class="form-label">Main Category</label>
        <select name="main_category" class="form-select" required>
            <option value="">-- Select --</option>
            <option value="Veg">Veg</option>
            <option value="Non-Veg">Non-Veg</option>
        </select>
        </div>

        <div class="mb-3">
        <label class="form-label">Sub Category</label>
        <select name="sub_category" class="form-select" required>
            <option value="">-- Select --</option>
            <option value="Pizza">Pizza</option>
            <option value="Biryani">Biryani</option>
            <option value="Snacks">Snacks</option>
            <option value="South Indian">South Indian</option>
            <option value="Chinese">Chinese</option>
            <option value="Thali">Thali</option>
            <option value="Street Food">Street Food</option>
            <option value="Desserts">Desserts</option>
            <option value="Salads">Salads</option>
            <option value="Breakfast">Breakfast</option>
        </select>
        </div>


        <div class="mb-3">
          <label class="form-label">Price (₹)</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Image</label>
          <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <div class="d-grid">
          <button type="submit" name="add" class="btn btn-primary">Add Item</button>
        </div>
      </form>

      <div class="text-center mt-3">
        <a href="menu_items.php">← Back to Menu Items</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
