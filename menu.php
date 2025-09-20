<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
require 'db.php';

// Filter + Search
$search = $_GET['search'] ?? '';
$main_category = $_GET['main_category'] ?? '';
$sub_category = $_GET['sub_category'] ?? '';

$where = "1";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $where .= " AND name LIKE '%$search%'";
}
if (!empty($main_category)) {
    $main_category = $conn->real_escape_string($main_category);
    $where .= " AND main_category = '$main_category'";
}
if (!empty($sub_category)) {
    $sub_category = $conn->real_escape_string($sub_category);
    $where .= " AND sub_category = '$sub_category'";
}

$result = $conn->query("SELECT * FROM menu_items WHERE $where ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Our Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .card {
      border-radius: 10px;
      overflow: hidden;
      transition: all 0.3s ease;
    }
    .card:hover {
      transform: scale(1.01);
    }
    .card-img-top {
      height: 260px;
      object-fit: cover;
      border-bottom-left-radius: 30%;
      transition: transform 0.3s ease-in-out;
    }
    .card-img-top:hover {
      transform: scale(1.03);
    }
    .card-body h3 {
      font-weight: bold;
      font-family: monospace;
      font-style: italic;
    }
    .card-body i:hover {
      color: rgb(247, 153, 46);
      cursor: pointer;
    }
  </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-info">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">HungerHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
<ul class="navbar-nav ms-auto">
  <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
  <li class="nav-item"><a class="nav-link active" href="menu.php">Menu</a></li>
  <li class="nav-item"><a class="nav-link" href="#">About</a></li>
  <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>

  <?php if ($is_logged_in): ?>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle text-light" href="#" data-bs-toggle="dropdown">
        Welcome, <?= htmlspecialchars($user_name) ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="user/profile.php">My Profile</a></li>
        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
      </ul>
    </li>
  <?php else: ?>
    <li class="nav-item"><a class="nav-link btn btn-warning text-dark ms-2 px-3" href="user/login.php">Login</a></li>
  <?php endif; ?> 
  <li class="nav-item">
  <a class="nav-link position-relative" href="cart.php">
    <i class="fa-solid fa-cart-shopping"></i>
    Cart
    <?php if ($cart_count > 0): ?>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?= $cart_count ?>
      </span>
    <?php endif; ?>
  </a>
</li>

</ul>

    </div>
  </div>
</nav>

<!-- Section -->
<div class="container py-5">
  <h2 class="text-center mb-5" data-aos="fade-down">Explore Our <span class="text-primary">Delicious Menu</span></h2>

  <!-- Filter + Search -->
  <form method="get" class="row g-3 mb-4" data-aos="fade-down">
    <div class="col-md-3">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search food...">
    </div>
    <div class="col-md-3">
      <select name="main_category" class="form-select">
        <option value="">All Main Categories</option>
        <option value="Veg" <?= $main_category == 'Veg' ? 'selected' : '' ?>>Veg</option>
        <option value="Non-Veg" <?= $main_category == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
      </select>
    </div>
    <div class="col-md-3">
      <select name="sub_category" class="form-select">
        <option value="">All Sub Categories</option>
        <?php
        $subs = ['Pizza', 'Biryani', 'Snacks', 'South Indian', 'Chinese', 'Thali', 'Street Food', 'Desserts', 'Salads', 'Breakfast'];
        foreach ($subs as $sub) {
          $sel = ($sub_category == $sub) ? 'selected' : '';
          echo "<option value='$sub' $sel>$sub</option>";
        }
        ?>
      </select>
    </div>
    <div class="col-md-3 d-flex">
      <button type="submit" class="btn btn-primary me-2">Filter</button>
      <a href="menu.php" class="btn btn-secondary">Reset</a>
    </div>
  </form>

  <!-- Menu Grid -->
  <div class="row g-4">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-4" data-aos="fade-up">
          <div class="card h-100 shadow-sm text-center p-3 d-flex flex-column">
            <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top mb-2" alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="card-body d-flex flex-column justify-content-between">
              <h3 class="card-title"><?= htmlspecialchars($row['name']) ?></h3>
              <p class="mb-1"><strong><?= htmlspecialchars($row['main_category']) ?></strong> | <?= htmlspecialchars($row['sub_category']) ?></p>
              <p class="text-muted"><?= htmlspecialchars($row['description']) ?></p>
              <h6 class="text-success">₹ <?= number_format($row['price'], 2) ?></h6>
              <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-danger mt-3">
  <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
</a>

            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center">No menu items found.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
