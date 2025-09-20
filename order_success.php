<?php
session_start();
if (!isset($_SESSION['success'])) {
    header("Location: menu.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order Success - HungerHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
  <h2 class="text-success mb-4">🎉 Order Placed Successfully!</h2>
  <p><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
  <a href="menu.php" class="btn btn-primary mt-3">Back to Menu</a>
</div>
</body>
</html>
