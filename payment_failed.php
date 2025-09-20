<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment Failed - HungerHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h4><i class="fas fa-times-circle"></i> Payment Failed</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                        </div>

                        <h5 class="text-danger mb-3">Payment Could Not Be Processed</h5>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <p class="text-muted mb-4">
                            Your payment was not successful. This could be due to:
                        </p>

                        <ul class="list-unstyled text-start mb-4">
                            <li><i class="fas fa-check text-muted"></i> Insufficient funds in your account</li>
                            <li><i class="fas fa-check text-muted"></i> Network connectivity issues</li>
                            <li><i class="fas fa-check text-muted"></i> Card details entered incorrectly</li>
                            <li><i class="fas fa-check text-muted"></i> Payment gateway timeout</li>
                        </ul>

                        <div class="d-grid gap-2">
                            <a href="checkout.php" class="btn btn-primary">
                                <i class="fas fa-retry"></i> Try Payment Again
                            </a>
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-cart"></i> Review Cart
                            </a>
                            <a href="menu.php" class="btn btn-outline-info">
                                <i class="fas fa-utensils"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Support Information -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h6><i class="fas fa-headset"></i> Need Help?</h6>
                        <p class="text-muted mb-2">
                            If you continue to experience issues, please contact our support team.
                        </p>
                        <small class="text-muted">
                            <i class="fas fa-phone"></i> +91-XXXXXXXXXX &nbsp;|&nbsp;
                            <i class="fas fa-envelope"></i> support@hungerhub.com
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>