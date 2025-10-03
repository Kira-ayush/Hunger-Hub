<?php
session_start();
require 'db.php';

// Set response header for AJAX
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data'
    ]);
    exit();
}

$action = $input['action'] ?? '';
$item_id = intval($input['item_id'] ?? 0);

if (!$action || !$item_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit();
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];

try {
    if ($action === 'update') {
        $quantity = max(1, intval($input['quantity'] ?? 1));

        // Check if item exists in database
        $stmt = $conn->prepare("SELECT id, name, price FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Item not found'
            ]);
            exit();
        }

        // Update quantity in cart
        if (isset($cart[$item_id])) {
            $cart[$item_id]['quantity'] = $quantity;
        } else {
            $cart[$item_id] = ['quantity' => $quantity];
        }

        $message = 'Cart updated successfully';
    } elseif ($action === 'remove') {
        // Remove item from cart
        if (isset($cart[$item_id])) {
            unset($cart[$item_id]);
            $message = 'Item removed from cart';
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Item not found in cart'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        exit();
    }

    // Calculate new cart total
    $cart_total = 0;
    $cart_count = 0;

    if (!empty($cart)) {
        $ids = implode(',', array_keys($cart));
        $sql = "SELECT id, price FROM menu_items WHERE id IN ($ids)";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $quantity = $cart[$id]['quantity'];
            $cart_total += $row['price'] * $quantity;
            $cart_count += $quantity;
        }
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'cart_total' => number_format($cart_total, 2),
        'cart_count' => $cart_count,
        'cart_empty' => empty($cart)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
