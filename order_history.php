<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}
require 'db.php';

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order History - HungerHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link active" href="order_history.php">My Orders</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" data-bs-toggle="dropdown">
                            Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="user/profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="order_history.php">Order History</a></li>
                            <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="mb-4" data-aos="fade-right">
            <i class="fas fa-history"></i> My Order History
        </h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($order = $result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Order #<?= $order['id'] ?></h6>
                                <?php
                                $status_color = '';
                                switch ($order['status'] ?? 'Pending') {
                                    case 'Pending':
                                        $status_color = 'warning';
                                        break;
                                    case 'Confirmed':
                                        $status_color = 'info';
                                        break;
                                    case 'Preparing':
                                        $status_color = 'primary';
                                        break;
                                    case 'Ready':
                                        $status_color = 'success';
                                        break;
                                    case 'Out for Delivery':
                                        $status_color = 'dark';
                                        break;
                                    case 'Delivered':
                                        $status_color = 'success';
                                        break;
                                    case 'Cancelled':
                                        $status_color = 'danger';
                                        break;
                                    default:
                                        $status_color = 'secondary';
                                }
                                ?>
                                <span class="badge bg-<?= $status_color ?>"><?= htmlspecialchars($order['status'] ?? 'Pending') ?></span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6><i class="fas fa-utensils"></i> Items Ordered:</h6>
                                    <p class="text-muted"><?= htmlspecialchars($order['items']) ?></p>
                                </div>

                                <div class="mb-3">
                                    <h6><i class="fas fa-map-marker-alt"></i> Delivery Address:</h6>
                                    <p class="text-muted"><?= nl2br(htmlspecialchars($order['address'])) ?></p>
                                </div>

                                <div class="mb-3">
                                    <h6><i class="fas fa-phone"></i> Contact:</h6>
                                    <p class="text-muted"><?= htmlspecialchars($order['phone']) ?></p>
                                </div>

                                <?php if (!empty($order['admin_notes'])): ?>
                                    <div class="mb-3">
                                        <h6><i class="fas fa-sticky-note"></i> Order Notes:</h6>
                                        <p class="text-muted small"><?= htmlspecialchars($order['admin_notes']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-success">₹<?= number_format($order['total'], 2) ?></strong>
                                </div>
                                <small class="text-muted">
                                    <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Order Status Legend -->
            <div class="mt-5" data-aos="fade-up">
                <h5>Order Status Guide:</h5>
                <div class="row g-2">
                    <div class="col-auto"><span class="badge bg-warning">Pending</span> - Order received</div>
                    <div class="col-auto"><span class="badge bg-info">Confirmed</span> - Order confirmed</div>
                    <div class="col-auto"><span class="badge bg-primary">Preparing</span> - Being prepared</div>
                    <div class="col-auto"><span class="badge bg-success">Ready</span> - Ready for pickup</div>
                    <div class="col-auto"><span class="badge bg-dark">Out for Delivery</span> - On the way</div>
                    <div class="col-auto"><span class="badge bg-success">Delivered</span> - Order delivered</div>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center" data-aos="fade-up">
                <div class="card shadow p-5">
                    <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                    <h4>No Orders Yet</h4>
                    <p class="text-muted">You haven't placed any orders yet. Start browsing our delicious menu!</p>
                    <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>