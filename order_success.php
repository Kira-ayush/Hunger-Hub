<?php
session_start();
if (!isset($_SESSION['success'])) {
  header("Location: menu.php");
  exit();
}

$payment_method = $_SESSION['payment_method'] ?? 'COD';
$transaction_id = $_SESSION['transaction_id'] ?? null;
$upi_id = $_SESSION['upi_id'] ?? null;
$payment_details = $_SESSION['payment_details'] ?? null;
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Order Success - HungerHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    .success-animation {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.1);
      }

      100% {
        transform: scale(1);
      }
    }
  </style>
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body text-center">
            <div class="mb-4">
              <i class="fas fa-check-circle text-success success-animation" style="font-size: 5rem;"></i>
            </div>

            <h2 class="text-success mb-3">🎉 Order Placed Successfully!</h2>

            <div class="alert alert-success">
              <?= htmlspecialchars($_SESSION['success']) ?>
              <?php if ($payment_method === 'UPI'): ?>
                <br><small class="text-muted">
                  <i class="fas fa-shield-alt"></i> Your UPI payment has been processed securely.
                </small>
              <?php endif; ?>
            </div>

            <!-- Payment Details -->
            <div class="card bg-light mb-4">
              <div class="card-body">
                <h6><i class="fas fa-credit-card"></i> Payment Information</h6>
                <div class="row">
                  <div class="col-md-6">
                    <strong>Payment Method:</strong><br>
                    <?php
                    switch ($payment_method) {
                      case 'COD':
                        echo '<i class="fas fa-money-bill-wave text-info"></i> Cash on Delivery';
                        break;
                      case 'UPI':
                        echo '<i class="fas fa-mobile-alt text-success"></i> UPI Payment';
                        break;
                      case 'RAZORPAY':
                        echo '<i class="fas fa-credit-card text-primary"></i> Razorpay';
                        break;
                      case 'PayPal':
                        echo '<i class="fab fa-paypal text-primary"></i> PayPal';
                        break;
                      default:
                        echo '<i class="fas fa-question-circle"></i> ' . htmlspecialchars($payment_method);
                    }
                    ?>
                  </div>
                  <?php if ($transaction_id): ?>
                    <div class="col-md-6">
                      <strong>Transaction ID:</strong><br>
                      <code><?= htmlspecialchars($transaction_id) ?></code>
                    </div>
                  <?php endif; ?>
                  <?php if ($payment_method === 'UPI' && $upi_id): ?>
                    <div class="col-md-6 mt-2">
                      <strong>UPI ID Used:</strong><br>
                      <small class="text-muted"><?= htmlspecialchars($upi_id) ?></small>
                    </div>
                  <?php endif; ?>
                  <?php if ($payment_details && isset($payment_details['timestamp'])): ?>
                    <div class="col-md-6 mt-2">
                      <strong>Payment Time:</strong><br>
                      <small class="text-muted"><?= htmlspecialchars($payment_details['timestamp']) ?></small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Next Steps -->
            <div class="row mb-4">
              <div class="col-md-4">
                <div class="card h-100">
                  <div class="card-body text-center">
                    <i class="fas fa-clock text-warning mb-2" style="font-size: 2rem;"></i>
                    <h6>Order Processing</h6>
                    <small class="text-muted">Your order is being prepared</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card h-100">
                  <div class="card-body text-center">
                    <i class="fas fa-truck text-primary mb-2" style="font-size: 2rem;"></i>
                    <h6>Out for Delivery</h6>
                    <small class="text-muted">We'll notify you when it's on the way</small>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card h-100">
                  <div class="card-body text-center">
                    <i class="fas fa-home text-success mb-2" style="font-size: 2rem;"></i>
                    <h6>Delivered</h6>
                    <small class="text-muted">Enjoy your meal!</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2 d-md-block">
              <a href="order_history.php" class="btn btn-primary">
                <i class="fas fa-history"></i> View Order History
              </a>
              <a href="menu.php" class="btn btn-success">
                <i class="fas fa-utensils"></i> Order Again
              </a>
              <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-home"></i> Go Home
              </a>
            </div>
          </div>
        </div>

        <!-- Thank You Message -->
        <div class="text-center mt-4">
          <h5 class="text-muted">Thank you for choosing HungerHub!</h5>
          <p class="text-muted">
            We hope you enjoy your meal. Your feedback helps us serve you better.
          </p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Clean up session variables
unset($_SESSION['success']);
unset($_SESSION['payment_method']);
unset($_SESSION['transaction_id']);
unset($_SESSION['upi_id']);
unset($_SESSION['payment_details']);
?>