<?php
session_start();
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // If cart exists
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$id] = ['quantity' => 1];
    }

    $_SESSION['success'] = "Item added to cart!";
    header("Location: menu.php");
    exit();
} else {
    header("Location: menu.php");
    exit();
}
