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
            $_SESSION['user_address'] = $user['address'] ?? '';
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HungerHub</title>

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
            --primary-color: #ff6b35;
            --secondary-color: #2c3e50;
            --accent-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../images/login-pattern.svg') center/cover;
            opacity: 0.1;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .login-form {
            padding: 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
            height: auto;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        .form-floating label {
            color: #6c757d;
            font-weight: 500;
        }

        .input-group-text {
            border: 2px solid #e9ecef;
            border-right: none;
            background: #f8f9fa;
            border-radius: 12px 0 0 12px;
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }

        .btn-login {
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
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.3);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            margin: 0 -2.5rem -2.5rem;
            border-top: 2px solid var(--primary-color);
        }

        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .register-link a:hover {
            color: white;
            background: var(--primary-color);
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        .alert {
            border: none;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #51cf66, #40c057);
            color: white;
        }

        .alert-info {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            color: white;
        }

        .back-home {
            position: absolute;
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
        }

        .back-home:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 1rem;
                border-radius: 15px;
            }

            .login-form {
                padding: 2rem 1.5rem;
            }

            .register-link {
                margin: 0 -1.5rem -2rem;
            }
        }

        /* Animation classes */
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
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
    <a href="../index.php" class="back-home" data-aos="fade-down">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container" data-aos="zoom-in" data-aos-duration="800">
                    <div class="login-header">
                        <div class="brand-logo floating">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h2>Welcome Back!</h2>
                        <p>Sign in to your HungerHub account</p>
                    </div>

                    <div class="login-form">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" data-aos="shake">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" data-aos="fade-in">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($_SESSION['success']) ?>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <?php if (isset($_GET['logged_out'])): ?>
                            <div class="alert alert-info" data-aos="fade-in">
                                <i class="fas fa-info-circle me-2"></i>
                                You have been successfully logged out. Thank you for using HungerHub!
                            </div>
                        <?php endif; ?>

                        <form method="post" id="loginForm" data-aos="fade-up" data-aos-delay="200">
                            <div class="form-floating" data-aos="fade-up" data-aos-delay="300">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                            </div>

                            <div class="form-floating position-relative" data-aos="fade-up" data-aos-delay="400">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3" data-aos="fade-up" data-aos-delay="500">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-muted text-decoration-none">
                                    <small>Forgot Password?</small>
                                </a>
                            </div>

                            <button type="submit" class="btn btn-login" data-aos="fade-up" data-aos-delay="600">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Sign In
                            </button>

                            <div class="text-center mt-3" data-aos="fade-up" data-aos-delay="650">
                                <p class="mb-2 text-muted">New to HungerHub?</p>
                                <a href="register.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus me-2"></i>Create New Account
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="register-link" data-aos="fade-up" data-aos-delay="700">
                        <p class="mb-2 text-dark font-weight-bold">
                            <i class="fas fa-user-plus me-2"></i>New to HungerHub?
                        </p>
                        <a href="register.php">
                            <i class="fas fa-arrow-right me-1"></i>Join us today - Create your free account!
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

        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Form validation and enhancement
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
            submitBtn.disabled = true;

            // Re-enable button after 3 seconds as fallback
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
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