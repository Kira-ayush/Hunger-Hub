<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name  = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $pass);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Email already registered.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>User Registration</title></head>
<body>
<h2>Register</h2>
<?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); } ?>
<form method="post">
  <input type="text" name="name" placeholder="Full Name" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="text" name="phone" placeholder="Phone" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
