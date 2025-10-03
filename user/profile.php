<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));

    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $address, $user_id);

    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name;
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_address'] = $address;
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile.";
    }
    header("Location: profile.php");
    exit();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match.";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (password_verify($current_password, $user['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hash, $user_id);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Password changed successfully!";
            } else {
                $_SESSION['error'] = "Error changing password.";
            }
        } else {
            $_SESSION['error'] = "Current password is incorrect.";
        }
    }
    header("Location: profile.php");
    exit();
}

// Get user's order history
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_orders = $stmt->get_result();

// Get user statistics
$stmt = $conn->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(total), 0) as total_spent FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - HungerHub</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #2c3e50;
            --accent-color: #a29bfe;
            --success-color: #00b894;
            --warning-color: #fdcb6e;
            --danger-color: #e84393;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .profile-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.3);
        }

        .profile-name {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .profile-email {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: black;
            display: block;
        }

        .stat-label {
            color: rgba(0, 0, 0, 0.8);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: var(--dark-text);
        }

        .card-header i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
        }

        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(108, 92, 231, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(108, 92, 231, 0.3);
        }

        .order-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .order-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .order-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .alert {
            border: none;
            border-radius: 12px;
            font-weight: 500;
        }

        .nav-tabs {
            border: none;
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 12px;
            padding: 0.8rem 1.5rem;
            margin-right: 0.5rem;
            color: var(--dark-text);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
        }

        .nav-tabs .nav-link:hover {
            background: rgba(108, 92, 231, 0.1);
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 1000;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .content-card {
                margin: 1rem;
                padding: 1.5rem;
            }

            .profile-header {
                margin: 1rem;
                padding: 1.5rem;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body>
    <a href="../menu.php" class="back-btn" data-aos="fade-down">
        <i class="fas fa-arrow-left me-2"></i>Back to Menu
    </a>

    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header" data-aos="zoom-in">
            <div class="profile-avatar floating">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($_SESSION['user_name']) ?></h1>
            <p class="profile-email">
                <i class="fas fa-envelope me-2"></i>
                <?= htmlspecialchars($_SESSION['user_email']) ?>
            </p>
            <p class="profile-email">
                <i class="fas fa-phone me-2"></i>
                <?= htmlspecialchars($_SESSION['user_phone']) ?>
            </p>
            <p class="profile-email">
                <i class="fas fa-map-marker-alt me-2"></i>
                <?= htmlspecialchars($_SESSION['user_address'] ?? 'No address provided') ?>
            </p>

            <div class="stats-container ">
                <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                    <span class="stat-number"><?= $stats['total_orders'] ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                    <span class="stat-number">₹<?= number_format($stats['total_spent'], 2) ?></span>
                    <span class="stat-label">Total Spent</span>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" data-aos="fade-in">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" data-aos="shake">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs justify-content-center" data-aos="fade-up">
            <li class="nav-item">
                <a class="nav-link active" href="#profile-info" data-bs-toggle="tab">
                    <i class="fas fa-user me-2"></i>Profile Info
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#order-history" data-bs-toggle="tab">
                    <i class="fas fa-history me-2"></i>Order History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#security" data-bs-toggle="tab">
                    <i class="fas fa-shield-alt me-2"></i>Security
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Profile Info Tab -->
            <div class="tab-pane fade show active" id="profile-info">
                <div class="content-card" data-aos="fade-up">
                    <div class="card-header">
                        <i class="fas fa-edit"></i>
                        <h5>Edit Profile Information</h5>
                    </div>

                    <form method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                                    <label for="name">
                                        <i class="fas fa-user me-2"></i>Full Name
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?= htmlspecialchars($_SESSION['user_phone']) ?>" required>
                                    <label for="phone">
                                        <i class="fas fa-phone me-2"></i>Phone Number
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email"
                                value="<?= htmlspecialchars($_SESSION['user_email']) ?>" disabled>
                            <label for="email">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <div class="form-text">Email cannot be changed</div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="address" name="address" placeholder="Complete Address"
                                required style="min-height: 100px; resize: vertical;"><?= htmlspecialchars($_SESSION['user_address'] ?? '') ?></textarea>
                            <label for="address">
                                <i class="fas fa-map-marker-alt me-2"></i>Complete Address
                            </label>
                            <div class="form-text">Your delivery address for orders</div>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order History Tab -->
            <div class="tab-pane fade" id="order-history">
                <div class="content-card" data-aos="fade-up">
                    <div class="card-header">
                        <i class="fas fa-history"></i>
                        <h5>Recent Orders</h5>
                    </div>

                    <?php if ($recent_orders->num_rows > 0): ?>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <div class="order-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">Order #<?= $order['id'] ?></h6>
                                        <p class="mb-1 text-muted">
                                            <i class="fas fa-calendar me-2"></i>
                                            <?= date('M d, Y - h:i A', strtotime($order['created_at'])) ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-rupee-sign me-1"></i>
                                            <strong>₹<?= number_format($order['total'], 2) ?></strong>
                                        </p>
                                    </div>
                                    <span class="order-status status-<?= strtolower($order['status']) ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <div class="text-center mt-3">
                            <a href="../order_history.php" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>View All Orders
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No orders yet</h5>
                            <p class="text-muted">Start ordering delicious food!</p>
                            <a href="../menu.php" class="btn btn-primary">
                                <i class="fas fa-utensils me-2"></i>Browse Menu
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Security Tab -->
            <div class="tab-pane fade" id="security">
                <div class="content-card" data-aos="fade-up">
                    <div class="card-header">
                        <i class="fas fa-key"></i>
                        <h5>Change Password</h5>
                    </div>

                    <form method="post" id="passwordForm">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="current_password"
                                name="current_password" required>
                            <label for="current_password">
                                <i class="fas fa-lock me-2"></i>Current Password
                            </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="new_password"
                                name="new_password" required minlength="6">
                            <label for="new_password">
                                <i class="fas fa-key me-2"></i>New Password
                            </label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirm_password"
                                name="confirm_password" required>
                            <label for="confirm_password">
                                <i class="fas fa-key me-2"></i>Confirm New Password
                            </label>
                            <div class="form-text">
                                <small id="passwordMatch"></small>
                            </div>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="fas fa-shield-alt me-2"></i>Change Password
                        </button>
                    </form>
                </div>

                <div class="content-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header">
                        <i class="fas fa-sign-out-alt"></i>
                        <h5>Account Actions</h5>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true
        });

        // Password confirmation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');

            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                return;
            }

            if (newPassword === confirmPassword) {
                matchText.innerHTML = '<i class="fas fa-check text-success"></i> Passwords match';
                matchText.className = 'text-success';
            } else {
                matchText.innerHTML = '<i class="fas fa-times text-danger"></i> Passwords do not match';
                matchText.className = 'text-danger';
            }
        });

        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
                return;
            }
        });

        // Focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-floating').style.transform = 'scale(1.02)';
                this.closest('.form-floating').style.transition = 'transform 0.2s ease';
            });

            input.addEventListener('blur', function() {
                this.closest('.form-floating').style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>