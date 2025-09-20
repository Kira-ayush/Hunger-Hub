<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 1) {
        $user = $res->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_phone'] = $user['phone'];
            header("Location: ../menu.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
        }
    } else {
        $_SESSION['error'] = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>User Login</title></head>
<body>
<h2>Login</h2>
<?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>".$_SESSION['error']."</p>"; unset($_SESSION['error']); } ?>
<form method="post">
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Login</button>
</form>
<p>No account? <a href="register.php">Register here</a></p>
</body>
</html>
