<?php

/**
 * Payment Configuration for HungerHub
 * 
 * This file contains all payment gateway configurations and settings.
 * Make sure to keep this file secure and never commit actual API keys to version control.
 */

// Environment configuration
define('PAYMENT_ENVIRONMENT', 'sandbox'); // 'sandbox' for testing, 'production' for live

// Currency settings
define('DEFAULT_CURRENCY', 'INR');
define('SUPPORTED_CURRENCIES', ['INR', 'USD', 'EUR', 'GBP']);

// Razorpay Configuration
$razorpay_config = [
    'sandbox' => [
        'key_id' => 'rzp_test_YOUR_ACTUAL_TEST_KEY_ID',     // Replace with your test Key ID
        'key_secret' => 'YOUR_ACTUAL_TEST_SECRET_KEY',      // Replace with your test Secret Key
        'webhook_secret' => 'YOUR_ACTUAL_WEBHOOK_SECRET',   // Generate in Dashboard → Webhooks
    ],
    'production' => [
        'key_id' => 'rzp_live_YOUR_ACTUAL_LIVE_KEY_ID',     // Replace with your live Key ID (after KYC)
        'key_secret' => 'YOUR_ACTUAL_LIVE_SECRET_KEY',      // Replace with your live Secret Key
        'webhook_secret' => 'YOUR_ACTUAL_LIVE_WEBHOOK_SECRET', // Live webhook secret
    ]
];

// PayPal Configuration
$paypal_config = [
    'sandbox' => [
        'client_id' => 'YOUR_PAYPAL_SANDBOX_CLIENT_ID',
        'client_secret' => 'YOUR_PAYPAL_SANDBOX_CLIENT_SECRET',
        'base_url' => 'https://api.sandbox.paypal.com',
        'webhook_id' => 'YOUR_WEBHOOK_ID',
    ],
    'production' => [
        'client_id' => 'YOUR_PAYPAL_LIVE_CLIENT_ID',
        'client_secret' => 'YOUR_PAYPAL_LIVE_CLIENT_SECRET',
        'base_url' => 'https://api.paypal.com',
        'webhook_id' => 'YOUR_LIVE_WEBHOOK_ID',
    ]
];

// Payment method configurations
$payment_methods = [
    'COD' => [
        'enabled' => true,
        'name' => 'Cash on Delivery',
        'description' => 'Pay when your order is delivered',
        'icon' => 'fas fa-money-bill-wave',
        'min_amount' => 0,
        'max_amount' => 2000, // Max COD limit in INR
        'fee' => 0,
        'processing_time' => 'Instant'
    ],
    'RAZORPAY' => [
        'enabled' => false, // Disabled - only UPI through direct UPI option
        'name' => 'Razorpay',
        'description' => 'Pay securely with UPI, Cards, Netbanking & Wallets',
        'icon' => 'fas fa-credit-card',
        'min_amount' => 1,
        'max_amount' => 100000,
        'fee_percentage' => 2.36, // Razorpay fee percentage
        'processing_time' => 'Instant',
        'supported_methods' => ['card', 'netbanking', 'wallet', 'upi', 'emi']
    ],
    'PAYPAL' => [
        'enabled' => false, // Disabled - not needed
        'name' => 'PayPal',
        'description' => 'Pay securely with PayPal',
        'icon' => 'fab fa-paypal',
        'min_amount' => 1,
        'max_amount' => 50000,
        'fee_percentage' => 2.9,
        'processing_time' => 'Instant',
        'supported_currencies' => ['USD', 'EUR', 'GBP', 'INR']
    ],
    'UPI' => [
        'enabled' => true,
        'name' => 'UPI Payment',
        'description' => 'Pay instantly using UPI ID or scan QR code',
        'icon' => 'fas fa-mobile-alt',
        'min_amount' => 1,
        'max_amount' => 100000,
        'fee' => 0,
        'processing_time' => 'Instant'
    ]
];

// Get current environment configuration
function getPaymentConfig($gateway)
{
    global $razorpay_config, $paypal_config, $payment_methods;

    $env = PAYMENT_ENVIRONMENT;
    $gateway_upper = strtoupper($gateway);

    // For gateway credentials
    switch (strtolower($gateway)) {
        case 'razorpay':
        case 'RAZORPAY':
            $config = $razorpay_config[$env];
            $config['environment'] = $env;
            return $config;
        case 'paypal':
        case 'PAYPAL':
            $config = $paypal_config[$env];
            $config['environment'] = $env;
            return $config;
        case 'cod':
        case 'COD':
            return $payment_methods['COD'];
        default:
            // Return payment method configuration if exists
            if (isset($payment_methods[$gateway_upper])) {
                return $payment_methods[$gateway_upper];
            }
            return null;
    }
}

// Payment processing settings
$payment_settings = [
    'auto_capture' => true, // Automatically capture payments
    'payment_timeout' => 15, // Payment timeout in minutes
    'max_retry_attempts' => 3,
    'webhook_timeout' => 30, // Webhook processing timeout in seconds
    'refund_processing_days' => 7, // Days to process refunds
    'transaction_fee_bearer' => 'merchant', // 'customer' or 'merchant'
];

// Security settings
$security_settings = [
    'allowed_ips' => [], // Leave empty for all IPs, or specify allowed IPs
    'rate_limit' => [
        'requests_per_minute' => 60,
        'requests_per_hour' => 1000,
    ],
    'encryption_key' => 'your-encryption-key-here', // Change this in production
    'webhook_signature_validation' => true,
];

// Notification settings
$notification_settings = [
    'email_notifications' => [
        'payment_success' => true,
        'payment_failed' => true,
        'refund_processed' => true,
    ],
    'sms_notifications' => [
        'payment_success' => false, // Enable when SMS service is integrated
        'payment_failed' => false,
    ],
    'admin_notifications' => [
        'payment_failures' => true,
        'high_value_transactions' => true,
        'refund_requests' => true,
    ]
];

// Currency conversion settings (if supporting multiple currencies)
$currency_settings = [
    'auto_conversion' => false,
    'conversion_api' => 'fixer.io', // or 'currencyapi.com'
    'conversion_api_key' => 'YOUR_CURRENCY_API_KEY',
    'update_interval' => 3600, // Update rates every hour
];

// Logging configuration
$logging_config = [
    'log_level' => 'INFO', // DEBUG, INFO, WARNING, ERROR
    'log_payments' => true,
    'log_webhooks' => true,
    'log_refunds' => true,
    'log_file_path' => 'logs/payments.log',
    'max_log_file_size' => '10MB',
];

// Helper functions
function getPaymentMethods()
{
    global $payment_methods;
    $enabled_methods = [];

    foreach ($payment_methods as $method_key => $method_config) {
        if (is_array($method_config) && isset($method_config['enabled']) && $method_config['enabled']) {
            $enabled_methods[$method_key] = $method_config;
        }
    }

    return $enabled_methods;
}

/**
 * Get available payment methods for the checkout page
 * @return array Available payment methods with their configurations
 */
function getAvailablePaymentMethods()
{
    return getPaymentMethods();
}

function calculatePaymentFee($amount, $method)
{
    global $payment_methods;

    if (!isset($payment_methods[$method])) {
        return 0;
    }

    $config = $payment_methods[$method];

    if (isset($config['fee'])) {
        return $config['fee'];
    }

    if (isset($config['fee_percentage'])) {
        return ($amount * $config['fee_percentage']) / 100;
    }

    return 0;
}

function isPaymentMethodAvailable($method, $amount, $currency = 'INR')
{
    global $payment_methods;

    if (!isset($payment_methods[$method]) || !$payment_methods[$method]['enabled']) {
        return false;
    }

    $config = $payment_methods[$method];

    // Check amount limits
    if ($amount < $config['min_amount'] || $amount > $config['max_amount']) {
        return false;
    }

    // Check currency support
    if (isset($config['supported_currencies']) && !in_array($currency, $config['supported_currencies'])) {
        return false;
    }

    return true;
}

function logPaymentEvent($event, $data = [])
{
    global $logging_config;

    if (!$logging_config['log_payments']) {
        return;
    }

    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $event,
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];

    // In production, implement proper logging mechanism
    error_log(json_encode($log_entry), 3, $logging_config['log_file_path']);
}

// Validation functions
function validatePaymentAmount($amount, $currency = 'INR')
{
    if (!is_numeric($amount) || $amount <= 0) {
        return false;
    }

    // Check minimum amount based on currency
    $min_amounts = [
        'INR' => 1,
        'USD' => 0.5,
        'EUR' => 0.5,
        'GBP' => 0.3
    ];

    return $amount >= ($min_amounts[$currency] ?? 1);
}

function sanitizePaymentData($data)
{
    if (is_array($data)) {
        return array_map('sanitizePaymentData', $data);
    }

    return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
}

// Constants for payment status
define('PAYMENT_STATUS_PENDING', 'Pending');
define('PAYMENT_STATUS_PROCESSING', 'Processing');
define('PAYMENT_STATUS_COMPLETED', 'Completed');
define('PAYMENT_STATUS_FAILED', 'Failed');
define('PAYMENT_STATUS_REFUNDED', 'Refunded');

// Constants for order status related to payments
define('ORDER_STATUS_PAYMENT_PENDING', 'Payment Pending');
define('ORDER_STATUS_PAYMENT_CONFIRMED', 'Payment Confirmed');
define('ORDER_STATUS_PAYMENT_FAILED', 'Payment Failed');
