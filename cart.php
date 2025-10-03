<?php
session_start();
require 'db.php';

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$total_price = 0;

if (!empty($cart)) {
  $ids = implode(',', array_keys($cart));
  $sql = "SELECT * FROM menu_items WHERE id IN ($ids)";
  $result = $conn->query($sql);
  while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $quantity = $cart[$id]['quantity'];
    $subtotal = $row['price'] * $quantity;
    $total_price += $subtotal;

    $cart_items[] = [
      'id' => $id,
      'name' => $row['name'],
      'image' => $row['image'],
      'price' => $row['price'],
      'quantity' => $quantity,
      'subtotal' => $subtotal
    ];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cart - HungerHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">

  <?php include 'includes/navbar.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4">🛒 Your Cart</h2>

    <?php if (empty($cart_items)): ?>
      <div class="alert alert-info">Your cart is empty.</div>
      <a href="menu.php" class="btn btn-primary">Browse Menu</a>
    <?php else: ?>
      <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Image</th>
            <th>Item</th>
            <th>Price (₹)</th>
            <th>Quantity</th>
            <th>Subtotal (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart_items as $item): ?>
            <tr>
              <td><img src="<?= htmlspecialchars($item['image']) ?>" width="70" height="70" style="object-fit:cover;"></td>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= number_format($item['price'], 2) ?></td>
              <td><?= $item['quantity'] ?></td>
              <td><?= number_format($item['subtotal'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
          <tr class="table-light">
            <td colspan="4" class="text-end"><strong>Total:</strong></td>
            <td><strong>₹ <?= number_format($total_price, 2) ?></strong></td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex justify-content-between">
        <a href="clear_cart.php" class="btn btn-outline-danger">Clear Cart</a>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
      </div>
    <?php endif; ?>
  </div>

  <?php include 'includes/footer.php'; ?>

</body>

</html>