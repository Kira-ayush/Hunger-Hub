<?php
session_start();
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $phone   = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $pass    = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $pass);

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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HungerHub</title>

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
            --success-color: #28a745;
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

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }

        .register-header {
            background: linear-gradient(135deg, var(--success-color), #20c997);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .register-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../images/register-pattern.svg') center/cover;
            opacity: 0.1;
        }

        .register-header h2 {
            margin: 0;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .register-header p {
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

        .register-form {
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
            border-color: var(--success-color);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .form-floating label {
            color: #6c757d;
            font-weight: 500;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            margin: 0.5rem 0;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak {
            background: #dc3545;
            width: 25%;
        }

        .strength-fair {
            background: #ffc107;
            width: 50%;
        }

        .strength-good {
            background: #fd7e14;
            width: 75%;
        }

        .strength-strong {
            background: #28a745;
            width: 100%;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--success-color), #20c997);
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
            color: white;
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-outline-success {
            border: 2px solid var(--success-color);
            color: var(--success-color);
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-outline-success:hover {
            background: var(--success-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3);
        }

        .login-link {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            margin: 0 -2.5rem -2.5rem;
            border-top: 2px solid var(--success-color);
        }

        .login-link a {
            color: var(--success-color);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .login-link a:hover {
            color: white;
            background: var(--success-color);
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
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

        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .form-check-input:focus {
            border-color: var(--success-color);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .terms-link {
            color: var(--success-color);
            text-decoration: none;
        }

        .terms-link:hover {
            color: #20c997;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .register-container {
                margin: 1rem;
                border-radius: 15px;
            }

            .register-form {
                padding: 2rem 1.5rem;
            }

            .login-link {
                margin: 0 -1.5rem -2rem;
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
    <a href="../index.php" class="back-home" data-aos="fade-down">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-container" data-aos="zoom-in" data-aos-duration="800">
                    <div class="register-header">
                        <div class="brand-logo floating">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2>Join HungerHub!</h2>
                        <p>Create your account and start ordering</p>
                    </div>

                    <div class="register-form">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" data-aos="shake">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form method="post" id="registerForm" data-aos="fade-up" data-aos-delay="200">
                            <div class="form-floating" data-aos="fade-up" data-aos-delay="300">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                <label for="name">
                                    <i class="fas fa-user me-2"></i>Full Name
                                </label>
                            </div>

                            <div class="form-floating" data-aos="fade-up" data-aos-delay="400">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                            </div>

                            <div class="form-floating" data-aos="fade-up" data-aos-delay="500">
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required pattern="[0-9]{10}">
                                <label for="phone">
                                    <i class="fas fa-phone me-2"></i>Phone Number
                                </label>
                                <div class="form-text">
                                    <small class="text-muted">Enter 10-digit phone number</small>
                                </div>
                            </div>

                            <div class="form-floating" data-aos="fade-up" data-aos-delay="550">
                                <textarea class="form-control" id="address" name="address" placeholder="Complete Address" required style="min-height: 100px; resize: vertical;"></textarea>
                                <label for="address">
                                    <i class="fas fa-map-marker-alt me-2"></i>Complete Address
                                </label>
                                <div class="form-text">
                                    <small class="text-muted">Enter your complete delivery address</small>
                                </div>
                            </div>

                            <div class="form-floating" data-aos="fade-up" data-aos-delay="650">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required minlength="6">
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="password-strength">
                                    <div class="strength-bar">
                                        <div class="strength-fill" id="strengthBar"></div>
                                    </div>
                                    <small id="strengthText" class="text-muted">Password strength: <span id="strengthLevel">Weak</span></small>
                                </div>
                            </div>

                            <div class="form-floating" data-aos="fade-up" data-aos-delay="750">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                                <label for="confirmPassword">
                                    <i class="fas fa-lock me-2"></i>Confirm Password
                                </label>
                                <div class="form-text">
                                    <small id="passwordMatch" class="text-muted"></small>
                                </div>
                            </div>

                            <div class="form-check" data-aos="fade-up" data-aos-delay="850">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="terms-link">Terms & Conditions</a> and <a href="#" class="terms-link">Privacy Policy</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-register" id="registerBtn" data-aos="fade-up" data-aos-delay="950">
                                <i class="fas fa-user-plus me-2"></i>
                                Create Account
                            </button>

                            <div class="text-center mt-3" data-aos="fade-up" data-aos-delay="1000">
                                <p class="mb-2 text-muted">Already have an account?</p>
                                <a href="login.php" class="btn btn-outline-success">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In Instead
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="login-link" data-aos="fade-up" data-aos-delay="1100">
                        <p class="mb-2 text-dark font-weight-bold">
                            <i class="fas fa-sign-in-alt me-2"></i>Already part of HungerHub?
                        </p>
                        <a href="login.php">
                            <i class="fas fa-arrow-right me-1"></i>Welcome back - Sign in to your account!
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

        // Password strength checker
        function checkPasswordStrength(password) {
            let score = 0;
            let feedback = 'Weak';
            let className = 'strength-weak';

            // Length check
            if (password.length >= 8) score += 1;
            if (password.length >= 12) score += 1;

            // Character variety
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;

            // Determine strength
            if (score <= 2) {
                feedback = 'Weak';
                className = 'strength-weak';
            } else if (score <= 4) {
                feedback = 'Fair';
                className = 'strength-fair';
            } else if (score <= 5) {
                feedback = 'Good';
                className = 'strength-good';
            } else {
                feedback = 'Strong';
                className = 'strength-strong';
            }

            return {
                score,
                feedback,
                className
            };
        }

        // Password input handler
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthLevel = document.getElementById('strengthLevel');

            if (password.length === 0) {
                strengthBar.className = 'strength-fill';
                strengthLevel.textContent = 'Enter password';
                strengthLevel.style.color = '#6c757d';
                return;
            }

            const strength = checkPasswordStrength(password);
            strengthBar.className = `strength-fill ${strength.className}`;
            strengthLevel.textContent = strength.feedback;

            // Color the text based on strength
            const colors = {
                'Weak': '#dc3545',
                'Fair': '#ffc107',
                'Good': '#fd7e14',
                'Strong': '#28a745'
            };
            strengthLevel.style.color = colors[strength.feedback];
        });

        // Confirm password handler
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchText = document.getElementById('passwordMatch');

            if (confirmPassword.length === 0) {
                matchText.textContent = '';
                return;
            }

            if (password === confirmPassword) {
                matchText.innerHTML = '<i class="fas fa-check text-success"></i> Passwords match';
                matchText.className = 'text-success';
            } else {
                matchText.innerHTML = '<i class="fas fa-times text-danger"></i> Passwords do not match';
                matchText.className = 'text-danger';
            }
        });

        // Phone number formatting
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            this.value = value;
        });

        // Form submission handler
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const submitBtn = document.getElementById('registerBtn');
            const originalText = submitBtn.innerHTML;

            // Check password match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return;
            }

            // Check password strength
            const strength = checkPasswordStrength(password);
            if (strength.score < 3) {
                e.preventDefault();
                alert('Please choose a stronger password!');
                return;
            }

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
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