<?php
session_start();
require 'db.php';

// Set response header for AJAX
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $quantity = isset($_GET['quantity']) ? max(1, intval($_GET['quantity'])) : 1; // Default to 1, minimum 1

    // Get item details for response
    $stmt = $conn->prepare("SELECT name, price FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item) {
        // If cart exists
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity; // Add the specified quantity
        } else {
            $_SESSION['cart'][$id] = ['quantity' => $quantity]; // Set the specified quantity
        }

        // Calculate cart totals
        $cart_count = 0;
        $cart_total = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $cart_id => $cart_item) {
                $cart_count += $cart_item['quantity'];

                // Get item price for total calculation
                $price_stmt = $conn->prepare("SELECT price FROM menu_items WHERE id = ?");
                $price_stmt->bind_param("i", $cart_id);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_row = $price_result->fetch_assoc();
                if ($price_row) {
                    $cart_total += $price_row['price'] * $cart_item['quantity'];
                }
            }
        }

        // Check if this is an AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            // Return JSON response for AJAX
            echo json_encode([
                'success' => true,
                'message' => $quantity > 1 ? $quantity . ' x ' . $item['name'] . ' added to cart!' : $item['name'] . ' added to cart!',
                'cart_count' => $cart_count,
                'cart_total' => number_format($cart_total, 2),
                'item_name' => $item['name'],
                'item_price' => number_format($item['price'], 2),
                'quantity_added' => $quantity
            ]);
            exit();
        } else {
            // Fallback for non-AJAX requests
            $_SESSION['success'] = "Item added to cart!";
            header("Location: menu.php");
            exit();
        }
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode([
                'success' => false,
                'message' => 'Item not found!'
            ]);
            exit();
        } else {
            header("Location: menu.php");
            exit();
        }
    }
} else {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request!'
        ]);
        exit();
    } else {
        header("Location: menu.php");
        exit();
    }
}
