<?php
session_start();

// If logout confirmation is received
if (isset($_POST['confirm_logout']) || isset($_GET['force'])) {
    session_destroy();
    header("Location: login.php?logged_out=1");
    exit();
}

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - HungerHub</title>

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
            --primary-color: #e74c3c;
            --secondary-color: #2c3e50;
            --accent-color: #c0392b;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .logout-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 450px;
            margin: 0 auto;
        }

        .logout-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .logout-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../images/logout-pattern.svg') center/cover;
            opacity: 0.1;
        }

        .logout-header h2 {
            margin: 0;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .logout-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .logout-content {
            padding: 2.5rem;
            text-align: center;
        }

        .user-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
        }

        .user-email {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .logout-message {
            color: var(--dark-text);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .btn-logout {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-right: 1rem;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3);
            color: white;
        }

        .btn-logout:active {
            transform: translateY(0);
        }

        .btn-logout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-logout:hover::before {
            left: 100%;
        }

        .btn-cancel {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cancel:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(231, 76, 60, 0.3);
        }

        .logout-footer {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            margin: 0 -2.5rem -2.5rem;
            border-top: 1px solid #dee2e6;
        }

        .logout-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-footer a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .logout-container {
                margin: 1rem;
                border-radius: 15px;
            }

            .logout-content {
                padding: 2rem 1.5rem;
            }

            .logout-footer {
                margin: 0 -1.5rem -2rem;
            }

            .btn-logout,
            .btn-cancel {
                display: block;
                width: 100%;
                margin-bottom: 1rem;
                margin-right: 0;
            }
        }

        /* Animation classes */
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

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="logout-container" data-aos="zoom-in" data-aos-duration="800">
                    <div class="logout-header">
                        <div class="brand-logo floating">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h2>Logout Confirmation</h2>
                        <p>Are you sure you want to sign out?</p>
                    </div>

                    <div class="logout-content">
                        <div class="user-info" data-aos="fade-up" data-aos-delay="200">
                            <div class="user-avatar pulse">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                            <div class="user-email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
                        </div>

                        <p class="logout-message" data-aos="fade-up" data-aos-delay="300">
                            <i class="fas fa-info-circle me-2"></i>
                            You will be signed out of your HungerHub account and redirected to the login page.
                        </p>

                        <form method="post" class="d-inline" data-aos="fade-up" data-aos-delay="400">
                            <button type="submit" name="confirm_logout" class="btn btn-logout">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Yes, Logout
                            </button>
                        </form>

                        <a href="profile.php" class="btn-cancel" data-aos="fade-up" data-aos-delay="500">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                    </div>

                    <div class="logout-footer" data-aos="fade-up" data-aos-delay="600">
                        <p class="mb-0">
                            <small class="text-muted">
                                Need help? <a href="../index.php">Visit our homepage</a>
                            </small>
                        </p>
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

        // Auto-logout after 30 seconds of inactivity on this page
        let logoutTimer;

        function resetLogoutTimer() {
            clearTimeout(logoutTimer);
            logoutTimer = setTimeout(() => {
                if (confirm('You have been inactive. Do you want to logout now?')) {
                    window.location.href = 'logout.php?force=1';
                }
            }, 30000); // 30 seconds
        }

        // Reset timer on user activity
        document.addEventListener('mousemove', resetLogoutTimer);
        document.addEventListener('keypress', resetLogoutTimer);
        document.addEventListener('click', resetLogoutTimer);

        // Initialize timer
        resetLogoutTimer();

        // Add confirmation dialog
        document.querySelector('form').addEventListener('submit', function(e) {
            const confirmed = confirm('Are you sure you want to logout?');
            if (!confirmed) {
                e.preventDefault();
            }
        });

        // Keyboard shortcut for quick logout (Ctrl + L)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                if (confirm('Quick logout with Ctrl+L. Continue?')) {
                    window.location.href = 'logout.php?force=1';
                }
            }
        });
    </script>
</body>

</html>