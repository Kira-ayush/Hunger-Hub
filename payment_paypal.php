<?php
session_start();
require_once 'db.php';
require_once 'payment_config.php';

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

// Get PayPal configuration
$paypal_config = getPaymentConfig('PAYPAL');
$paypal_client_id = $paypal_config['client_id'];
$paypal_environment = $paypal_config['environment']; // 'sandbox' or 'production'

// Convert INR to USD (approximate rate - in production, use real-time rates)
$usd_amount = $amount / 82; // Approximate conversion rate
$usd_amount = round($usd_amount, 2);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PayPal Payment - HungerHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- PayPal SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id ?>&currency=USD&components=buttons"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><i class="fab fa-paypal text-primary"></i> PayPal Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h5>Order #<?= $order_id ?></h5>
                            <p class="text-muted"><?= $order['items'] ?></p>
                            <div class="row">
                                <div class="col-6">
                                    <strong>INR Amount:</strong><br>
                                    <span class="text-success">₹<?= number_format($amount, 2) ?></span>
                                </div>
                                <div class="col-6">
                                    <strong>USD Amount:</strong><br>
                                    <span class="text-primary">$<?= number_format($usd_amount, 2) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal Buttons Container -->
                        <div id="paypal-button-container"></div>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i>
                                Secure payment powered by PayPal
                            </small>
                        </div>

                        <div class="text-center mt-3">
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
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= $usd_amount ?>'
                        },
                        description: 'HungerHub Order #<?= $order_id ?>',
                        custom_id: '<?= $order_id ?>',
                        reference_id: 'HUNGERHUB_<?= $order_id ?>'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Payment successful
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'payment_paypal_success.php';

                    var orderIdInput = document.createElement('input');
                    orderIdInput.type = 'hidden';
                    orderIdInput.name = 'order_id';
                    orderIdInput.value = '<?= $order_id ?>';
                    form.appendChild(orderIdInput);

                    var transactionIdInput = document.createElement('input');
                    transactionIdInput.type = 'hidden';
                    transactionIdInput.name = 'transaction_id';
                    transactionIdInput.value = details.id;
                    form.appendChild(transactionIdInput);

                    var payerEmailInput = document.createElement('input');
                    payerEmailInput.type = 'hidden';
                    payerEmailInput.name = 'payer_email';
                    payerEmailInput.value = details.payer.email_address;
                    form.appendChild(payerEmailInput);

                    var amountInput = document.createElement('input');
                    amountInput.type = 'hidden';
                    amountInput.name = 'amount';
                    amountInput.value = details.purchase_units[0].amount.value;
                    form.appendChild(amountInput);

                    document.body.appendChild(form);
                    form.submit();
                });
            },
            onError: function(err) {
                console.error('PayPal Error:', err);
                alert('Payment failed. Please try again.');
                window.location.href = 'payment_failed.php';
            },
            onCancel: function(data) {
                alert('Payment cancelled.');
                window.location.href = 'checkout.php';
            }
        }).render('#paypal-button-container');
    </script>

</body>

</html>