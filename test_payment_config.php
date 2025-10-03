<?php
// Test Payment Configuration
require 'payment_config.php';

echo "<h2>Payment Configuration Test</h2>";
echo "<h3>Available Payment Methods:</h3>";
echo "<ul>";

foreach (getPaymentMethods() as $method => $config) {
    $status = $config['enabled'] ? '✅ Enabled' : '❌ Disabled';
    echo "<li><strong>{$config['name']}</strong> ({$method}) - {$status}</li>";
    echo "<ul>";
    echo "<li>Description: {$config['description']}</li>";
    echo "<li>Min Amount: ₹{$config['min_amount']}</li>";
    echo "<li>Max Amount: ₹{$config['max_amount']}</li>";
    echo "</ul><br>";
}

echo "</ul>";

echo "<h3>Enabled Methods Only:</h3>";
echo "<ul>";
foreach (getAvailablePaymentMethods() as $method => $config) {
    echo "<li><strong>{$config['name']}</strong> - {$config['description']}</li>";
}
echo "</ul>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    ul {
        margin: 10px 0;
    }

    li {
        margin: 5px 0;
    }
</style>