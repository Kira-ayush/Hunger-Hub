<?php
session_start();
require_once 'db.php';
require_once 'payment_config.php';
require_once 'includes/RazorpayPayment.php';

// Initialize payment configuration
$payment_config = getPaymentConfig('RAZORPAY');
define('RAZORPAY_KEY_ID', $payment_config['key_id']);
define('RAZORPAY_KEY_SECRET', $payment_config['key_secret']);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

// Check if there's a pending order
if (!isset($_SESSION['pending_order_id']) || !isset($_SESSION['pending_amount'])) {
    header("Location: menu.php");
    exit();
}

$order_id = $_SESSION['pending_order_id'];
$amount = $_SESSION['pending_amount'];

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order not found.";
    header("Location: menu.php");
    exit();
}

$razorpay = new RazorpayPayment();

// Handle payment verification
if ($_POST && isset($_POST['razorpay_payment_id'])) {
    $payment_id = $_POST['razorpay_payment_id'];
    $order_id_razorpay = $_POST['razorpay_order_id'];
    $signature = $_POST['razorpay_signature'];

    // Verify payment
    $verification_result = $razorpay->verifyPayment($payment_id, $order_id_razorpay, $signature);

    if ($verification_result['success']) {
        // Payment successful - update order status
        $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Completed', payment_id = ? WHERE id = ?");
        $stmt->bind_param("si", $payment_id, $order_id);
        $stmt->execute();

        // Clear session data
        unset($_SESSION['cart']);
        unset($_SESSION['pending_order_id']);
        unset($_SESSION['pending_amount']);

        $_SESSION['success'] = "Payment successful! Your order has been confirmed.";
        header("Location: order_success.php");
        exit();
    } else {
        // Payment failed
        $_SESSION['error'] = "Payment verification failed: " . $verification_result['message'];
        header("Location: payment_failed.php");
        exit();
    }
}

// Create Razorpay order
$razorpay_order = $razorpay->createOrder($amount, $order_id, "HungerHub Order #" . $order_id);

if (!$razorpay_order['success']) {
    $_SESSION['error'] = "Failed to create payment order: " . $razorpay_order['message'];
    header("Location: checkout.php");
    exit();
}

$razorpay_order_id = $razorpay_order['order_id'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Razorpay Payment - HungerHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><i class="fas fa-credit-card text-primary"></i> Complete Payment</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <h5>Order #<?= $order_id ?></h5>
                            <p class="text-muted"><?= $order['items'] ?></p>
                            <h3 class="text-success">₹<?= number_format($amount, 2) ?></h3>
                        </div>

                        <button id="rzp-button1" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-lock"></i> Pay with Razorpay
                        </button>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i>
                                Secure payment powered by Razorpay
                            </small>
                        </div>

                        <div class="mt-3">
                            <a href="checkout.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var options = {
            "key": "<?= RAZORPAY_KEY_ID ?>",
            "amount": "<?= $amount * 100 ?>", // Amount in paise
            "currency": "INR",
            "name": "HungerHub",
            "description": "Order #<?= $order_id ?>",
            "image": "images/logo.png",
            "order_id": "<?= $razorpay_order_id ?>",
            "handler": function(response) {
                // Create form and submit payment details
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '';

                var paymentId = document.createElement('input');
                paymentId.type = 'hidden';
                paymentId.name = 'razorpay_payment_id';
                paymentId.value = response.razorpay_payment_id;
                form.appendChild(paymentId);

                var orderId = document.createElement('input');
                orderId.type = 'hidden';
                orderId.name = 'razorpay_order_id';
                orderId.value = response.razorpay_order_id;
                form.appendChild(orderId);

                var signature = document.createElement('input');
                signature.type = 'hidden';
                signature.name = 'razorpay_signature';
                signature.value = response.razorpay_signature;
                form.appendChild(signature);

                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "<?= $order['customer_name'] ?>",
                "contact": "<?= $order['phone'] ?>"
            },
            "notes": {
                "order_id": "<?= $order_id ?>",
                "customer_name": "<?= $order['customer_name'] ?>"
            },
            "theme": {
                "color": "#28a745"
            },
            "modal": {
                "ondismiss": function() {
                    window.location.href = 'checkout.php';
                }
            }
        };

        var rzp1 = new Razorpay(options);

        document.getElementById('rzp-button1').onclick = function(e) {
            rzp1.open();
            e.preventDefault();
        }

        // Auto-open payment modal
        rzp1.open();
    </script>

</body>

</html>