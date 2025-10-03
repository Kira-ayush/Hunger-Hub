<?php
// Get current page name for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Cart count calculation
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

// User authentication check
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="images/logo.png" alt="HungerHub Logo" width="40" height="40" class="me-2" />
            <span class="fw-bold text-warning">HungerHub</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'menu' ? 'active' : '' ?>" href="menu.php">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'about' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'contact' ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>

                <?php if ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" data-bs-toggle="dropdown">
                            Welcome, <?= htmlspecialchars($user_name) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="user/profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="order_history.php">Order History</a></li>
                            <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning text-dark ms-2 px-3" href="user/login.php">Login</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link position-relative <?= $current_page === 'cart' ? 'active' : '' ?>" href="cart.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                                <?= $cart_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Navbar End -->