<?php
session_start();
require 'db.php';
echo "Testing navbar include:\n";
echo "Current page: " . basename($_SERVER['PHP_SELF'], '.php') . "\n";
echo "Session cart: " . (isset($_SESSION['cart']) ? 'exists' : 'not exists') . "\n";
echo "User logged in: " . (isset($_SESSION['user_id']) ? 'yes' : 'no') . "\n";

echo "\n--- Navbar HTML ---\n";
include 'includes/navbar.php';
echo "\n--- End Navbar ---\n";
