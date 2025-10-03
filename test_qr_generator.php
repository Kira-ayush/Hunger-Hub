<?php
require 'qr_generator.php';

// Test QR generation
$test_upi = "upi://pay?pa=ranchianita2000@okaxis&pn=HungerHub&am=299.50&cu=INR&tr=HH123456";

echo "<h2>QR Generator Test</h2>";
echo "<p>Testing UPI link: <code>" . htmlspecialchars($test_upi) . "</code></p>";

echo "<h3>HTML QR Code:</h3>";
echo generateUPIQR($test_upi, 'html');

echo "<h3>SVG QR Code:</h3>";
echo generateUPIQR($test_upi, 'svg');

echo "<hr>";
echo "<p><a href='payment_upi.php'>Go to UPI Payment Page</a></p>";
?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="p-4">
</body>

</html>