<?php
session_start();
require 'db.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user/login.php");
    exit();
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

$total = 0;
$items_summary = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $user_id = $_SESSION['user_id'];

    // Fetch item details
    $ids = implode(',', array_keys($cart));
    $query = $conn->query("SELECT * FROM menu_items WHERE id IN ($ids)");
    while ($item = $query->fetch_assoc()) {
        $id = $item['id'];
        $qty = $cart[$id]['quantity'];
        $subtotal = $item['price'] * $qty;
        $total += $subtotal;
        $items_summary[] = "{$item['name']} (x$qty)";
    }

    $item_list = implode(", ", $items_summary);

    // Insert into orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, phone, address, items, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssd", $user_id, $name, $phone, $address, $item_list, $total);
    $stmt->execute();

    unset($_SESSION['cart']);
    $_SESSION['success'] = "Your order has been placed successfully!";
    header("Location: order_success.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Checkout - HungerHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4">🧾 Checkout</h2>

  <div class="row">
    <!-- Order Form -->
    <div class="col-md-6">
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Delivery Address</label>
          <textarea name="address" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success w-100">Place Order</button>
      </form>
    </div>

    <!-- Cart Summary -->
    <div class="col-md-6">
      <h5 class="mb-3">🛒 Your Cart Summary</h5>
      <ul class="list-group mb-3">
        <?php
        $ids = implode(',', array_keys($cart));
        $result = $conn->query("SELECT * FROM menu_items WHERE id IN ($ids)");
        $total = 0;
        while ($item = $result->fetch_assoc()):
            $id = $item['id'];
            $qty = $cart[$id]['quantity'];
            $subtotal = $item['price'] * $qty;
            $total += $subtotal;
        ?>
        <li class="list-group-item d-flex justify-content-between">
          <?= htmlspecialchars($item['name']) ?> (x<?= $qty ?>)
          <span>₹ <?= number_format($subtotal, 2) ?></span>
        </li>
        <?php endwhile; ?>
        <li class="list-group-item d-flex justify-content-between fw-bold">
          Total:
          <span>₹ <?= number_format($total, 2) ?></span>
        </li>
      </ul>
    </div>
  </div>
</div>
</body>
</html>
