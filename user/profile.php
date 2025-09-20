<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>My Profile</title></head>
<body>
<h2>👤 Profile</h2>
<p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['user_phone']) ?></p>

<a href="../menu.php">← Back to Menu</a>
</body>
</html>
