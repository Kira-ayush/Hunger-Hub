<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No item ID provided.";
    header("Location: menu_items.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    $_SESSION['error'] = "Item not found.";
    header("Location: menu_items.php");
    exit();
}

if (isset($_POST['update'])) {
    $name          = htmlspecialchars(trim($_POST['name']));
    $main_category = htmlspecialchars(trim($_POST['main_category']));
    $sub_category  = htmlspecialchars(trim($_POST['sub_category']));
    $price         = floatval($_POST['price']);
    $description   = htmlspecialchars(trim($_POST['description']));
    $image_path    = $item['image']; // existing image by default

    // Handle image upload if new image is selected
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $img_name = basename($image['name']);
        $img_tmp = $image['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($img_ext, $allowed_exts)) {
            $_SESSION['error'] = "Invalid image type.";
            header("Location: edit_menu_item.php?id=$id");
            exit();
        }

        $new_img_name = uniqid("FOOD_", true) . '.' . $img_ext;
        $image_path = "uploads/" . $new_img_name;

        if (!move_uploaded_file($img_tmp, "../" . $image_path)) {
            $_SESSION['error'] = "Image upload failed.";
            header("Location: edit_menu_item.php?id=$id");
            exit();
        }
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE menu_items SET name=?, main_category=?, sub_category=?, price=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sssdssi", $name, $main_category, $sub_category, $price, $description, $image_path, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Item updated successfully!";
        header("Location: menu_items.php");
        exit();
    } else {
        $_SESSION['error'] = "Update failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="col-md-8 mx-auto" data-aos="fade-up">
    <div class="card shadow p-4">
      <h3 class="mb-4 text-center">Edit Menu Item</h3>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Item Name</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Main Category</label>
          <select name="main_category" class="form-select" required>
            <option value="Veg" <?= $item['main_category'] == 'Veg' ? 'selected' : '' ?>>Veg</option>
            <option value="Non-Veg" <?= $item['main_category'] == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Sub Category</label>
          <select name="sub_category" class="form-select" required>
            <?php
            $subcategories = ['Pizza', 'Biryani', 'Snacks', 'South Indian', 'Chinese', 'Thali', 'Street Food', 'Desserts', 'Salads', 'Breakfast'];
            foreach ($subcategories as $sub) {
              $selected = ($item['sub_category'] == $sub) ? 'selected' : '';
              echo "<option value='$sub' $selected>$sub</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Price (₹)</label>
          <input type="number" step="0.01" name="price" class="form-control" value="<?= $item['price'] ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control"><?= htmlspecialchars($item['description']) ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Change Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <small class="text-muted">Current Image:</small><br>
          <img src="../<?= htmlspecialchars($item['image']) ?>" width="80" height="80" class="mt-2" style="object-fit: cover;">
        </div>

        <div class="d-grid">
          <button type="submit" name="update" class="btn btn-primary">Update Item</button>
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
