<?php
session_start();
require 'db.php';
require 'payment_config.php';
require 'qr_generator.php';

// Check if user has a pending order
if (!isset($_SESSION['pending_order_id']) || !isset($_SESSION['pending_amount'])) {
    header("Location: checkout.php");
    exit();
}

$order_id = $_SESSION['pending_order_id'];
$amount = $_SESSION['pending_amount'];

// UPI configuration
$merchant_vpa = "ranchianita2000@okaxis"; // Replace with your actual UPI ID
$merchant_name = "HungerHub";

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_payment'])) {
    $transaction_id = $_POST['transaction_id'] ?? '';
    $upi_id = $_POST['upi_id'] ?? '';

    if (empty($transaction_id)) {
        $_SESSION['error'] = "Please enter the transaction ID.";
    } else {
        // First, verify that the order exists
        $check_order = $conn->prepare("SELECT id, payment_status FROM orders WHERE id = ?");
        $check_order->bind_param("i", $order_id);
        $check_order->execute();
        $order_result = $check_order->get_result();

        if ($order_result->num_rows === 0) {
            $_SESSION['error'] = "Order not found. Please try again or contact support.";
        } else {
            $order_data = $order_result->fetch_assoc();

            if ($order_data['payment_status'] === 'Paid') {
                $_SESSION['error'] = "This order has already been paid.";
            } else {
                // Update order payment status (using existing columns only)
                $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid' WHERE id = ?");
                $stmt->bind_param("i", $order_id);

                if ($stmt->execute()) {
                    // Try to record payment in payments table if it exists
                    $payments_table_exists = false;
                    $check_result = $conn->query("SHOW TABLES LIKE 'payments'");
                    if ($check_result && $check_result->num_rows > 0) {
                        $payments_table_exists = true;

                        // Get all columns in payments table
                        $columns_result = $conn->query("SHOW COLUMNS FROM payments");
                        $available_columns = [];
                        if ($columns_result) {
                            while ($column = $columns_result->fetch_assoc()) {
                                $available_columns[] = $column['Field'];
                            }
                        }

                        // Build INSERT query based on available columns
                        $insert_columns = ['order_id'];
                        $insert_values = ['?'];
                        $param_types = 'i';
                        $param_values = [$order_id];

                        // Add user_id if column exists (required for foreign key)
                        if (in_array('user_id', $available_columns)) {
                            // Try to get user_id from session or use a default/test user
                            $user_id = $_SESSION['user_id'] ?? null;

                            if (!$user_id) {
                                // Find the first available user or create a guest entry
                                $user_check = $conn->query("SELECT id FROM users LIMIT 1");
                                if ($user_check && $user_check->num_rows > 0) {
                                    $user_data = $user_check->fetch_assoc();
                                    $user_id = $user_data['id'];
                                } else {
                                    // Skip payment record if no users exist and user_id is required
                                    error_log("Cannot insert payment: user_id required but no users found");
                                    $user_id = null;
                                }
                            }

                            if ($user_id) {
                                $insert_columns[] = 'user_id';
                                $insert_values[] = '?';
                                $param_types .= 'i';
                                $param_values[] = $user_id;
                            } else {
                                // Cannot insert without user_id, skip payment record
                                error_log("Skipping payment record: user_id is required but not available");
                                $insert_columns = []; // Clear to skip insert
                            }
                        }

                        // Only proceed if we still have columns to insert
                        if (!empty($insert_columns)) {

                            // Add amount if column exists
                            if (in_array('amount', $available_columns)) {
                                $insert_columns[] = 'amount';
                                $insert_values[] = '?';
                                $param_types .= 'd';
                                $param_values[] = $amount;
                            }

                            // Add payment_method if column exists
                            if (in_array('payment_method', $available_columns)) {
                                $insert_columns[] = 'payment_method';
                                $insert_values[] = '?';
                                $param_types .= 's';
                                $param_values[] = 'UPI';
                            }

                            // Add status if column exists
                            if (in_array('status', $available_columns)) {
                                $insert_columns[] = 'status';
                                $insert_values[] = '?';
                                $param_types .= 's';
                                $param_values[] = 'Success';
                            }

                            // Add currency if column exists
                            if (in_array('currency', $available_columns)) {
                                $insert_columns[] = 'currency';
                                $insert_values[] = '?';
                                $param_types .= 's';
                                $param_values[] = 'INR';
                            }

                            // Add gateway_payment_id if column exists
                            if (in_array('gateway_payment_id', $available_columns)) {
                                $insert_columns[] = 'gateway_payment_id';
                                $insert_values[] = '?';
                                $param_types .= 's';
                                $param_values[] = $transaction_id;
                            }

                            // Add created_at if column exists
                            if (in_array('created_at', $available_columns)) {
                                $insert_columns[] = 'created_at';
                                $insert_values[] = 'NOW()';
                            }

                            // Execute the dynamic INSERT
                            if (count($insert_columns) > 1) { // Only insert if we have more than just order_id
                                $sql = "INSERT INTO payments (" . implode(', ', $insert_columns) . ") VALUES (" . implode(', ', $insert_values) . ")";
                                $stmt = $conn->prepare($sql);
                                if ($stmt && !empty($param_values)) {
                                    $stmt->bind_param($param_types, ...$param_values);
                                    if (!$stmt->execute()) {
                                        // Log error but don't stop the payment process
                                        error_log("Payment insert failed: " . $stmt->error);
                                    }
                                }
                            } else {
                                // No suitable columns found, just log this
                                error_log("Payments table exists but no suitable columns found for payment record");
                            }
                        } // Close the "if (!empty($insert_columns))" block
                    } else {
                        // No payments table, just log this
                        error_log("No payments table found, skipping payment record");
                    }

                    // Clear cart and session
                    unset($_SESSION['cart']);
                    unset($_SESSION['pending_order_id']);
                    unset($_SESSION['pending_amount']);

                    // Store payment details in session for confirmation
                    $_SESSION['payment_details'] = [
                        'transaction_id' => $transaction_id,
                        'upi_id' => $upi_id,
                        'amount' => $amount,
                        'payment_method' => 'UPI',
                        'order_id' => $order_id,
                        'timestamp' => date('Y-m-d H:i:s')
                    ];

                    // Set individual session variables for order_success.php compatibility
                    $_SESSION['payment_method'] = 'UPI';
                    $_SESSION['transaction_id'] = $transaction_id;
                    $_SESSION['upi_id'] = $upi_id;

                    $_SESSION['success'] = "Payment successful! Your order has been confirmed. Transaction ID: " . $transaction_id;
                    header("Location: order_success.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Error updating payment status. Please contact support.";
                }
            }
        }
    }
}

// Generate UPI payment link
$upi_link = "upi://pay?pa=" . urlencode($merchant_vpa) .
    "&pn=" . urlencode($merchant_name) .
    "&am=" . $amount .
    "&cu=INR" .
    "&tr=" . uniqid("HH", true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment - HungerHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .payment-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .qr-code-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
        }

        .amount-display {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }

        .upi-instructions {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card payment-card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-mobile-alt me-2"></i>UPI Payment
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Amount Display -->
                        <div class="text-center mb-4">
                            <p class="text-muted mb-1">Amount to Pay</p>
                            <div class="amount-display">₹<?= number_format($amount, 2) ?></div>
                            <small class="text-muted">Order #<?= $order_id ?></small>
                        </div>

                        <!-- UPI Instructions -->
                        <div class="upi-instructions">
                            <h6><i class="fas fa-info-circle me-2"></i>How to Pay via UPI</h6>
                            <ol class="mb-0">
                                <li>Click "Pay Now" button below to open your UPI app</li>
                                <li>Or scan the QR code with any UPI app</li>
                                <li>Enter your UPI PIN to complete payment</li>
                                <li>Enter the transaction ID below after successful payment</li>
                            </ol>
                        </div>

                        <!-- QR Code Section -->
                        <div class="qr-code-container mb-4">
                            <h6>Scan QR Code to Pay</h6>
                            <div class="text-center">
                                <!-- Your Custom QR Code Image -->
                                <div class="mb-3">
                                    <img src="images/upi_qr_code.jpg"
                                        alt="UPI QR Code for <?= htmlspecialchars($merchant_vpa) ?>"
                                        class="img-fluid"
                                        style="max-width: 250px; border: 3px solid #28a745; border-radius: 15px; padding: 10px; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"
                                        onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='block';">
                                    <div class="mt-2 text-success">
                                        <i class="fas fa-qrcode me-2"></i>Scan with any UPI app to pay ₹<?= number_format($amount, 2) ?>
                                    </div>
                                </div>

                                <!-- Fallback if image doesn't load -->
                                <div id="qr-fallback" style="display: none;" class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>QR Code Not Available</h6>
                                    <p class="mb-2">Please use the details below for manual payment:</p>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>UPI ID:</strong><br>
                                            <code><?= htmlspecialchars($merchant_vpa) ?></code>
                                        </div>
                                        <div class="col-6">
                                            <strong>Amount:</strong><br>
                                            <code>₹<?= number_format($amount, 2) ?></code>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details Cards -->
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <div class="card border-primary">
                                            <div class="card-body text-center p-2">
                                                <small class="text-muted">UPI ID</small><br>
                                                <code style="font-size: 11px;"><?= htmlspecialchars($merchant_vpa) ?></code>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card border-success">
                                            <div class="card-body text-center p-2">
                                                <small class="text-muted">Amount</small><br>
                                                <strong class="text-success">₹<?= number_format($amount, 2) ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Actions -->
                        <div class="d-grid gap-2 mb-4">
                            <a href="<?= htmlspecialchars($upi_link) ?>" class="btn btn-success btn-lg">
                                <i class="fas fa-mobile-alt me-2"></i>Pay Now via UPI App
                            </a>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="copyUpiId()">
                                        <i class="fas fa-copy me-1"></i>Copy UPI ID
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-info w-100" onclick="copyAmount()">
                                        <i class="fas fa-rupee-sign me-1"></i>Copy Amount
                                    </button>
                                </div>
                            </div>
                            <div class="alert alert-info mt-2 mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                <strong>Payment Instructions:</strong><br>
                                1. Scan the QR code above, OR<br>
                                2. Use the "Pay Now" button, OR<br>
                                3. Copy UPI ID and pay manually in your UPI app
                            </div>
                        </div>

                        <!-- Payment Confirmation Form -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'];
                                                                                unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="border-top pt-4">
                            <h6>Confirm Your Payment</h6>
                            <div class="mb-3">
                                <label for="transaction_id" class="form-label">Transaction ID *</label>
                                <input type="text" class="form-control" id="transaction_id" name="transaction_id"
                                    placeholder="Enter UPI transaction ID" required>
                                <div class="form-text">You'll receive this ID after successful payment</div>
                            </div>
                            <div class="mb-3">
                                <label for="upi_id" class="form-label">Your UPI ID (Optional)</label>
                                <input type="text" class="form-control" id="upi_id" name="upi_id"
                                    placeholder="yourname@paytm">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="confirm_payment" class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i>Confirm Payment
                                </button>
                                <a href="checkout.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Checkout
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Copy Functions -->
    <script>
        // Copy UPI ID function
        function copyUpiId() {
            const upiId = '<?= $merchant_vpa ?>';
            if (navigator.clipboard) {
                navigator.clipboard.writeText(upiId).then(function() {
                    showToast('UPI ID copied: ' + upiId, 'success');
                }).catch(function() {
                    promptCopy('UPI ID', upiId);
                });
            } else {
                promptCopy('UPI ID', upiId);
            }
        }

        // Copy amount function
        function copyAmount() {
            const amount = '<?= $amount ?>';
            if (navigator.clipboard) {
                navigator.clipboard.writeText(amount).then(function() {
                    showToast('Amount copied: ₹' + amount, 'success');
                }).catch(function() {
                    promptCopy('Amount', amount);
                });
            } else {
                promptCopy('Amount', amount);
            }
        }

        // Fallback copy method
        function promptCopy(label, value) {
            const result = prompt('Copy this ' + label + ':', value);
            if (result !== null) {
                showToast(label + ' ready to copy', 'info');
            }
        }

        // Simple toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px; opacity: 0.95;';
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>${message}`;

            document.body.appendChild(toast);

            setTimeout(function() {
                toast.remove();
            }, 3000);
        }

        // Log for debugging
        console.log('✅ UPI Payment Page Loaded');
        console.log('📱 UPI ID: <?= $merchant_vpa ?>');
        console.log('💰 Amount: ₹<?= $amount ?>');
        console.log('🖼️ QR Image: images/upi_qr_code.png');
    </script>
</body>

</html>