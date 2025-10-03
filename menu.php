<?php
session_start();
require 'db.php';

// Filter + Search
$search = $_GET['search'] ?? '';
$main_category = $_GET['main_category'] ?? '';
$sub_category = $_GET['sub_category'] ?? '';

$where = "1";
if (!empty($search)) {
  $search = $conn->real_escape_string($search);
  $where .= " AND name LIKE '%$search%'";
}
if (!empty($main_category)) {
  $main_category = $conn->real_escape_string($main_category);
  $where .= " AND main_category = '$main_category'";
}
if (!empty($sub_category)) {
  $sub_category = $conn->real_escape_string($sub_category);
  $where .= " AND sub_category = '$sub_category'";
}

$result = $conn->query("SELECT * FROM menu_items WHERE $where ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Our Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .card {
      border-radius: 10px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: scale(1.01);
    }

    .card-img-top {
      height: 260px;
      object-fit: cover;
      border-bottom-left-radius: 30%;
      transition: transform 0.3s ease-in-out;
    }

    .card-img-top:hover {
      transform: scale(1.03);
    }

    .card-body h3 {
      font-weight: bold;
      font-family: monospace;
      font-style: italic;
    }

    .card-body i:hover {
      color: rgb(247, 153, 46);
      cursor: pointer;
    }

    /* Add to Cart Button Animations */
    .add-to-cart-btn {
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .add-to-cart-btn:disabled {
      opacity: 0.8;
    }

    .add-to-cart-btn .fa-spinner {
      animation: spin 1s linear infinite;
    }

    /* Quantity Control Styles */
    .quantity-control {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .quantity-control .input-group {
      width: fit-content;
      margin: 0 auto;
    }

    .quantity-btn {
      border: 1px solid #dee2e6;
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }

    .quantity-btn:hover {
      background-color: #dc3545;
      border-color: #dc3545;
      color: white;
    }

    .quantity-input {
      border-left: none;
      border-right: none;
      border-top: 1px solid #dee2e6;
      border-bottom: 1px solid #dee2e6;
      text-align: center;
      font-weight: 600;
    }

    .quantity-input:focus {
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
      border-color: #dc3545;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }

    /* Toast custom styling */
    .toast {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Cart badge animation */
    .cart-badge {
      transition: all 0.3s ease;
      animation: pulse 0.5s ease-in-out;
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.2);
      }

      100% {
        transform: scale(1);
      }
    }
  </style>
</head>

<body class="bg-light">

  <?php include 'includes/navbar.php'; ?>

  <!-- Menu Section -->
  <div class="container py-5">
    <h2 class="text-center mb-5" data-aos="fade-down">Explore Our <span class="text-primary">Delicious Menu</span></h2>

    <!-- Filter + Search -->
    <form method="get" class="row g-3 mb-4" data-aos="fade-down">
      <div class="col-md-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search food...">
      </div>
      <div class="col-md-3">
        <select name="main_category" class="form-select">
          <option value="">All Main Categories</option>
          <option value="Veg" <?= $main_category == 'Veg' ? 'selected' : '' ?>>Veg</option>
          <option value="Non-Veg" <?= $main_category == 'Non-Veg' ? 'selected' : '' ?>>Non-Veg</option>
        </select>
      </div>
      <div class="col-md-3">
        <select name="sub_category" class="form-select">
          <option value="">All Sub Categories</option>
          <?php
          $subs = ['Pizza', 'Biryani', 'Snacks', 'South Indian', 'Chinese', 'Thali', 'Street Food', 'Desserts', 'Salads', 'Breakfast'];
          foreach ($subs as $sub) {
            $sel = ($sub_category == $sub) ? 'selected' : '';
            echo "<option value='$sub' $sel>$sub</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-3 d-flex">
        <button type="submit" class="btn btn-primary me-2">Filter</button>
        <a href="menu.php" class="btn btn-secondary">Reset</a>
      </div>
    </form>

    <!-- Menu Grid -->
    <div class="row g-4">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-4" data-aos="fade-up">
            <div class="card h-100 shadow-sm text-center p-3 d-flex flex-column">
              <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top mb-2" alt="<?= htmlspecialchars($row['name']) ?>">
              <div class="card-body d-flex flex-column justify-content-between">
                <h3 class="card-title"><?= htmlspecialchars($row['name']) ?></h3>
                <p class="mb-1"><strong><?= htmlspecialchars($row['main_category']) ?></strong> | <?= htmlspecialchars($row['sub_category']) ?></p>
                <p class="text-muted"><?= htmlspecialchars($row['description']) ?></p>
                <h6 class="text-success">₹ <?= number_format($row['price'], 2) ?></h6>

                <!-- Quantity Controls -->
                <div class="quantity-control mt-2 mb-3">
                  <label class="form-label small text-muted">Quantity</label>
                  <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary quantity-btn" type="button" data-action="decrease" data-item-id="<?= $row['id'] ?>">
                      <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="form-control text-center quantity-input"
                      value="1" min="1" max="10"
                      data-item-id="<?= $row['id'] ?>"
                      style="max-width: 80px;">
                    <button class="btn btn-outline-secondary quantity-btn" type="button" data-action="increase" data-item-id="<?= $row['id'] ?>">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                </div>

                <button type="button" class="btn btn-danger add-to-cart-btn"
                  data-item-id="<?= $row['id'] ?>"
                  data-item-name="<?= htmlspecialchars($row['name']) ?>"
                  data-item-price="<?= number_format($row['price'], 2) ?>">
                  <i class="fa-solid fa-cart-shopping me-2"></i>
                  <span class="btn-text">Add to Cart</span>
                </button>

                <!-- Fallback for users with JavaScript disabled -->
                <noscript>
                  <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-danger mt-3">
                    <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
                  </a>
                </noscript>

              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-info text-center">No menu items found.</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

  <!-- Toast notification container -->
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
    <div id="cart-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="fas fa-shopping-cart text-success me-2"></i>
        <strong class="me-auto">Cart Updated</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body" id="toast-message">
        Item added to cart!
      </div>
    </div>
  </div>

  <script>
    AOS.init();

    // Quantity Control functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Handle quantity increase/decrease buttons
      const quantityButtons = document.querySelectorAll('.quantity-btn');
      const quantityInputs = document.querySelectorAll('.quantity-input');

      quantityButtons.forEach(button => {
        button.addEventListener('click', function() {
          const action = this.getAttribute('data-action');
          const itemId = this.getAttribute('data-item-id');
          const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
          let currentValue = parseInt(input.value);

          if (action === 'increase' && currentValue < parseInt(input.max)) {
            input.value = currentValue + 1;
          } else if (action === 'decrease' && currentValue > parseInt(input.min)) {
            input.value = currentValue - 1;
          }
        });
      });

      // Handle direct input changes
      quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
          let value = parseInt(this.value);
          const min = parseInt(this.min);
          const max = parseInt(this.max);

          if (isNaN(value) || value < min) {
            this.value = min;
          } else if (value > max) {
            this.value = max;
          }
        });
      });

      // AJAX Add to Cart functionality
      const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
      const toastElement = document.getElementById('cart-toast');
      const toast = new bootstrap.Toast(toastElement);

      addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
          const itemId = this.getAttribute('data-item-id');
          const itemName = this.getAttribute('data-item-name');
          const itemPrice = this.getAttribute('data-item-price');
          const quantityInput = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
          const quantity = parseInt(quantityInput.value);
          const btnText = this.querySelector('.btn-text');
          const btnIcon = this.querySelector('i');

          // Show loading state
          const originalText = btnText.textContent;
          btnText.textContent = 'Adding...';
          btnIcon.className = 'fas fa-spinner fa-spin me-2';
          this.disabled = true;

          // Make AJAX request with quantity
          fetch(`add_to_cart.php?id=${itemId}&quantity=${quantity}`, {
              method: 'GET',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Show success state
                btnText.textContent = 'Added!';
                btnIcon.className = 'fas fa-check me-2';
                this.classList.remove('btn-danger');
                this.classList.add('btn-success');

                // Update toast message
                document.getElementById('toast-message').innerHTML = `
                <strong>${data.quantity_added > 1 ? data.quantity_added + ' x ' : ''}${data.item_name}</strong> added to cart!<br>
                <small class="text-muted">Price: ₹${data.item_price} each | Cart Total: ₹${data.cart_total}</small>
              `;

                // Show toast notification
                toast.show();

                // Update cart badge if exists
                const cartBadge = document.querySelector('.cart-badge');
                if (cartBadge) {
                  cartBadge.textContent = data.cart_count;
                  cartBadge.style.display = data.cart_count > 0 ? 'inline' : 'none';

                  // Add pulse animation to cart badge
                  cartBadge.style.animation = 'none';
                  setTimeout(() => {
                    cartBadge.style.animation = 'pulse 0.5s ease-in-out';
                  }, 10);
                }

                // Reset button after 2 seconds
                setTimeout(() => {
                  btnText.textContent = originalText;
                  btnIcon.className = 'fa-solid fa-cart-shopping me-2';
                  this.classList.remove('btn-success');
                  this.classList.add('btn-danger');
                  this.disabled = false;
                }, 2000);

              } else {
                // Show error state
                btnText.textContent = 'Error!';
                btnIcon.className = 'fas fa-exclamation-triangle me-2';
                this.classList.remove('btn-danger');
                this.classList.add('btn-warning');

                // Show error toast
                document.getElementById('toast-message').innerHTML = `
                <strong>Error:</strong> ${data.message}
              `;
                toastElement.querySelector('.toast-header strong').textContent = 'Error';
                toastElement.querySelector('.toast-header i').className = 'fas fa-exclamation-triangle text-warning me-2';
                toast.show();

                // Reset button after 2 seconds
                setTimeout(() => {
                  btnText.textContent = originalText;
                  btnIcon.className = 'fa-solid fa-cart-shopping me-2';
                  this.classList.remove('btn-warning');
                  this.classList.add('btn-danger');
                  this.disabled = false;
                }, 2000);
              }
            })
            .catch(error => {
              console.error('Error:', error);

              // Show error state
              btnText.textContent = 'Error!';
              btnIcon.className = 'fas fa-exclamation-triangle me-2';
              this.classList.remove('btn-danger');
              this.classList.add('btn-warning');

              // Show error toast
              document.getElementById('toast-message').innerHTML = `
              <strong>Network Error:</strong> Please try again.
            `;
              toastElement.querySelector('.toast-header strong').textContent = 'Network Error';
              toastElement.querySelector('.toast-header i').className = 'fas fa-exclamation-triangle text-warning me-2';
              toast.show();

              // Reset button after 2 seconds
              setTimeout(() => {
                btnText.textContent = originalText;
                btnIcon.className = 'fa-solid fa-cart-shopping me-2';
                this.classList.remove('btn-warning');
                this.classList.add('btn-danger');
                this.disabled = false;
              }, 2000);
            });
        });
      });
    });
  </script>

  <?php include 'includes/footer.php'; ?>
</body>

</html>