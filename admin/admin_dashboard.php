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
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$profile_img = $admin['profile_pic'] ?? 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- AOS CSS -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      min-height: 100vh;
      background-color: #343a40;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px 20px;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .profile-img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid white;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar d-flex flex-column p-4" data-aos="fade-right">
      <div class="text-center mb-4">
        <img src="../uploads/<?php echo htmlspecialchars($profile_img); ?>" class="profile-img mb-2" alt="Admin Image" />
        <h5><?php echo $_SESSION['admin_name']; ?></h5>
      </div>
      <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="menu_items.php">Menu Items</a>
        <a href="customers.php">Customers</a>
        <a href="messages.php">Messages</a>
        <a href="logout.php" class="mt-auto text-danger">Logout</a>
      </nav>

    </div>

    <!-- Main Content -->
    <div class="col-md-9 p-4" data-aos="fade-up">
      <h2 class="mb-4">Admin Dashboard</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow text-center p-3">
            <h4>Total Orders</h4>
            <p class="display-6 text-primary">123</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow text-center p-3">
            <h4>Menu Items</h4>
            <p class="display-6 text-success">45</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow text-center p-3">
            <h4>Users</h4>
            <p class="display-6 text-warning">78</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
