<?php
session_start();
require '../db.php';

if (isset($_POST['register'])) {
    $name     = htmlspecialchars(trim($_POST['name']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // File upload
    $profile_pic = $_FILES['profile_pic'];
    $target_dir = "../uploads/";
    $img_name = basename($profile_pic['name']);
    $img_tmp = $profile_pic['tmp_name'];
    $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($img_ext, $allowed_exts)) {
        $_SESSION['error'] = "Only JPG, JPEG, PNG, GIF allowed.";
        header("Location: admin_register.php");
        exit();
    }

    $new_img_name = uniqid("ADMIN_", true) . '.' . $img_ext;
    $img_path = $target_dir . $new_img_name;
    move_uploaded_file($img_tmp, $img_path);

    // Insert
    $stmt = $conn->prepare("INSERT INTO admins (name, email, password, profile_pic) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $img_path);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Admin registered successfully!";
        header("Location: admin_login.php");
    } else {
        $_SESSION['error'] = "Registration failed!";
        header("Location: admin_register.php");
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Registration</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- AOS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6" data-aos="zoom-in">
      <div class="card shadow p-4">
        <h3 class="text-center mb-4">Admin Registration</h3>

        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger text-center"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Profile Picture</label>
            <input type="file" name="profile_pic" class="form-control" accept="image/*" required />
          </div>
          <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="text-center mt-3">Already an admin? <a href="admin_login.php">Login here</a></p>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>
