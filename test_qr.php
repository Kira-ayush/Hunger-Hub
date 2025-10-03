<?php
// Test UPI payment page - for QR code debugging
$order_id = 12345;
$amount = 299.50;

// UPI configuration
$merchant_vpa = "ranchianita2000@okaxis";
$merchant_name = "HungerHub";

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
    <title>UPI Payment Test - HungerHub</title>
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

        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
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
                            <i class="fas fa-mobile-alt me-2"></i>UPI Payment Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Amount Display -->
                        <div class="text-center mb-4">
                            <p class="text-muted mb-1">Amount to Pay</p>
                            <div class="amount-display">₹<?= number_format($amount, 2) ?></div>
                            <small class="text-muted">Order #<?= $order_id ?></small>
                        </div>

                        <!-- Debug Information -->
                        <div class="debug-info">
                            <strong>Debug Information:</strong><br>
                            UPI Link: <?= htmlspecialchars($upi_link) ?><br>
                            Merchant VPA: <?= htmlspecialchars($merchant_vpa) ?><br>
                            Amount: <?= $amount ?><br>
                        </div>

                        <!-- QR Code Section -->
                        <div class="qr-code-container mb-4">
                            <h6>QR Code Generation Test</h6>
                            <div class="text-center">
                                <!-- Canvas QR Code -->
                                <div id="canvas-section">
                                    <h6>Canvas QR (JavaScript Library):</h6>
                                    <canvas id="qrcode" class="mb-3" style="max-width: 200px; border: 1px solid #ccc;"></canvas>
                                    <div id="canvas-status" class="alert alert-info">Loading...</div>
                                </div>

                                <!-- Google Charts QR Code -->
                                <div id="google-section" class="mt-4">
                                    <h6>Google Charts QR (Backup):</h6>
                                    <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?= urlencode($upi_link) ?>"
                                        alt="UPI QR Code" class="img-fluid"
                                        style="max-width: 200px; border: 1px solid #ddd; border-radius: 8px;"
                                        onload="document.getElementById('google-status').innerHTML = '✅ Google QR loaded successfully'"
                                        onerror="document.getElementById('google-status').innerHTML = '❌ Google QR failed to load'">
                                    <div id="google-status" class="alert alert-info mt-2">Loading...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Console Output -->
                        <div class="debug-info">
                            <strong>Console Log:</strong><br>
                            <div id="console-output">Check browser console for detailed logs...</div>
                        </div>

                        <!-- Test UPI Link -->
                        <div class="d-grid gap-2">
                            <a href="<?= htmlspecialchars($upi_link) ?>" class="btn btn-success btn-lg">
                                <i class="fas fa-mobile-alt me-2"></i>Test UPI Link
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for QR Code Generation -->
    <script src="https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Console logging helper
        function logToPage(message) {
            const output = document.getElementById('console-output');
            output.innerHTML += message + '<br>';
            console.log(message);
        }

        // QR Code generation with extensive debugging
        window.addEventListener('load', function() {
            logToPage('Page loaded, starting QR generation...');

            const upiLink = <?= json_encode($upi_link) ?>;
            const qrCanvas = document.getElementById('qrcode');
            const canvasStatus = document.getElementById('canvas-status');

            logToPage('UPI Link: ' + upiLink);
            logToPage('Canvas element found: ' + (qrCanvas ? 'Yes' : 'No'));
            logToPage('QRCode library available: ' + (typeof QRCode !== 'undefined' ? 'Yes' : 'No'));

            if (typeof QRCode === 'undefined') {
                canvasStatus.innerHTML = '❌ QRCode library not loaded';
                canvasStatus.className = 'alert alert-danger';
                logToPage('ERROR: QRCode library not available');
                return;
            }

            if (!qrCanvas) {
                canvasStatus.innerHTML = '❌ Canvas element not found';
                canvasStatus.className = 'alert alert-danger';
                logToPage('ERROR: Canvas element not found');
                return;
            }

            logToPage('Attempting to generate QR code...');

            // Try to generate QR code
            QRCode.toCanvas(qrCanvas, upiLink, {
                width: 200,
                height: 200,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            }).then(function() {
                canvasStatus.innerHTML = '✅ Canvas QR generated successfully';
                canvasStatus.className = 'alert alert-success';
                logToPage('SUCCESS: Canvas QR code generated');
            }).catch(function(error) {
                canvasStatus.innerHTML = '❌ Canvas QR generation failed: ' + error.message;
                canvasStatus.className = 'alert alert-danger';
                logToPage('ERROR: Canvas QR generation failed - ' + error.message);
                console.error('QR Code generation error:', error);
            });
        });

        // Check if external resources loaded
        window.addEventListener('DOMContentLoaded', function() {
            logToPage('DOM loaded');

            // Check Bootstrap
            if (typeof bootstrap !== 'undefined') {
                logToPage('Bootstrap library loaded');
            } else {
                logToPage('Bootstrap library NOT loaded');
            }
        });
    </script>
</body>

</html>