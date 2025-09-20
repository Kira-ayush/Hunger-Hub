<?php
session_start();
require 'db.php';
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HungerHub - Order Delicious Food Online</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- AOS Animation -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
    <link href="images/logo.png" rel="icon" type="image/png">
</head>

<body>

  <!-- Navbar Start -->
   
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/logo.png" alt="HungerHub Logo" width="40" height="40" class="me-2" />
        <span class="fw-bold text-warning">HungerHub</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
<ul class="navbar-nav ms-auto">
  <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
  <li class="nav-item"><a class="nav-link active" href="menu.php">Menu</a></li>
  <li class="nav-item"><a class="nav-link" href="#">About</a></li>
  <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>

  <?php if ($is_logged_in): ?>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle text-light" href="#" data-bs-toggle="dropdown">
        Welcome, <?= htmlspecialchars($user_name) ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="user/profile.php">My Profile</a></li>
        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
      </ul>
    </li>
  <?php else: ?>
    <li class="nav-item"><a class="nav-link btn btn-warning text-dark ms-2 px-3" href="user/login.php">Login</a></li>
  <?php endif; ?>
<li class="nav-item">
  <a class="nav-link position-relative" href="cart.php">
    <i class="fa-solid fa-cart-shopping"></i>
    Cart
    <?php if ($cart_count > 0): ?>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?= $cart_count ?>
      </span>
    <?php endif; ?>
  </a>
</li>

</a>
</ul>
      </div>
    </div>
  </nav>
  <!-- Navbar End -->

  <!-- Carousel Start -->
  <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="images/slide1.png" class="d-block w-100" alt="Delicious Dosa" />
        <div class="carousel-caption d-none d-md-block">
          <h2 class="fw-bold text-shadow">Crispy dosa with flavorful chutneys</h2>
          <p>Straight to your doorstep.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="images/slide2.png" class="d-block w-100" alt="Burger & Fries" />
        <div class="carousel-caption d-none d-md-block">
          <h2 class="fw-bold text-shadow">Juicy Burgers & Crispy Fries</h2>
          <p>The combo that everyone loves.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="images/slide3.png" class="d-block w-100" alt="Biryani Special" />
        <div class="carousel-caption d-none d-md-block">
          <h2 class="fw-bold text-shadow">Authentic Biryani Flavors</h2>
          <p>Spiced to perfection, every time.</p>
        </div>
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
  <!-- Carousel End -->

  <!-- About Section Start -->
<section class="py-5 bg-light" id="about">
  <div class="container">

    <!-- Section Heading -->
    <div class="text-center mb-5" data-aos="fade-in-right">
      <h1 class="fw-bold mb-3">About <span class="text-warning">HungerHub</span></h1>
      <p class="lead">HungerHub is your one-stop online destination for ordering delicious food anytime, anywhere.</p>
    </div>

    <!-- Row: Image + Intro -->
    <div class="row align-items-center mb-5">
      <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right">
        <img src="images/about_us.png" alt="About HungerHub" class="img-fluid rounded shadow" id="about_img" />
      </div>
      <div class="col-md-6" data-aos="fade-left">
        <h2 class="fw-bold mb-3 why-heading">Why Choose <span class="text-warning">HungerHub</span>?</h2><hr><br>
        <p>At HungerHub, we're passionate about delivering food that not only satisfies your hunger but also excites your taste buds. We carefully curate a variety of cuisines and partner with the best local kitchens to bring you quality and flavor in every bite.</p>
        <p>With an easy-to-use interface, real-time order tracking, and seamless payment options, we make food ordering enjoyable and hassle-free. Whether you're ordering lunch at the office or planning a family dinner, HungerHub has you covered.</p>
        <hr>
        <a href="#menu" class="btn btn-warning mt-3">Explore Menu</a>
      </div>
    </div>

    <!-- Animated Cards Row -->
    <div class="row justify-content-center text-center g-4" data-aos="zoom-in">
      <div class="col-md-4">
        <div class="card1">
          <div class="first-content">
            <span>🍕 Fresh & Fast</span>
          </div>
          <div class="second-content text-center flex-column">
            <span class="mb-2">🍕 Fresh & Fast</span>
            <p class="px-3">We prepare your food with the freshest ingredients and deliver it piping hot — right on time.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card1">
          <div class="first-content">
            <span>👨‍🍳 Expert Chefs</span>
          </div>
          <div class="second-content text-center flex-column">
            <span class="mb-2">👨‍🍳 Expert Chefs</span>
            <p class="px-3">Our kitchen is run by professionals who ensure every bite meets our quality standards.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card1">
          <div class="first-content">
            <span>📦 Safe Delivery</span>
          </div>
          <div class="second-content text-center flex-column">
            <span class="mb-2">📦 Safe Delivery</span>
            <p class="px-3">Your food is delivered with safety and care by our trained delivery partners.</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>


  <!-- About Section End -->

  <!-- Menu Section Start -->
<section class="py-5" id="menu">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold" data-aos="fade-down">Our <span class="text-warning">Menu</span></h2>

    <div class="row justify-content-center">
      <!-- Cheese Pizza -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm text-center">
          <img src="images/cheese_pizza.png" class="card-img-top mx-auto" alt="Cheese Pizza" />
          <div class="card-body">
            <h3>Cheese Pizza</h3>
            <p>Delicious cheese loaded pizza with Italian herbs.</p><br>
            <p class="fw-bold text-success">₹199</p>
            <a href="cart.php" class="btn btn-danger w-100">
              <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
            </a>
          </div>
        </div>
      </div>

      <!-- Chhole Bhature -->
             <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm text-center">
          <img src="images/chhole_bhature.png" class="card-img-top mx-auto" alt="Chhole Bhature" />
          <div class="card-body">
            <h3>Chhole Bhature</h3>
            <p>Authentic North Indian delight served with spicy chana and fluffy bhature.</p>
            <p class="fw-bold text-success">₹149</p>
            <a href="cart.php" class="btn btn-danger w-100">
              <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
            </a>
          </div>
        </div>
      </div>


      <!-- Chicken Biryani -->

        <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm text-center">
          <img src="images/chicken_biryani.png" class="card-img-top mx-auto" alt="Chicken Biryani" />
          <div class="card-body">
            <h3>Chicken Biryani</h3>
            <p>Spiced and flavorful Hyderabadi-style biryani.</p><br>
            <p class="fw-bold text-success">₹249</p>
            <a href="cart.php" class="btn btn-danger w-100">
              <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
            </a>
          </div>
        </div>
      </div>
      <div class="h-100 shadow-sm text-center">
      <a href="menu.php"><button class= "btn btn-primary ">View More</button></a>
    </div></div>
  </div>
</section>
  <!-- Menu Section End -->
<section class="py-5 bg-light" id="review">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold" data-aos="fade-down">
      What Our <span class="text-warning">Customers Say</span>
    </h2>
    <div class="row text-center">
      <!-- review 1 -->
      <div class="col-md-4 mb-4" data-aos="fade-right">
        <div class="review-card p-4 shadow rounded h-100">
          <p>"Amazing food and super quick delivery! HungerHub never disappoints."</p>
          <div class="rating my-2">
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star-half-alt text-warning"></i>
          </div>
          <h6 class="mt-3 fw-bold">– Rahul Sinha</h6>
        </div>
      </div>

      <!-- review 2 -->
      <div class="col-md-4 mb-4" data-aos="fade-up">
        <div class="review-card p-4 shadow rounded h-100">
          <p>"Love the app interface and the biryani is always top-notch!"</p>
          <div class="rating my-2">
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="far fa-star text-warning"></i>
          </div>
          <h6 class="mt-3 fw-bold">– Sneha Raj</h6>
        </div>
      </div>

      <!-- review 3 -->
      <div class="col-md-4 mb-4" data-aos="fade-left">
        <div class="review-card p-4 shadow rounded h-100">
          <p>"Affordable prices and great taste. My go-to food delivery site!"</p>
          <div class="rating my-2">
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
          </div>
          <h6 class="mt-3 fw-bold">– Vikash Kumar</h6>
        </div>
      </div>
    </div>
  </div>
</section>
  <!-- reviews end -->
    <!-- facts -->
     <section class="py-5 bg-light" id="stats">
  <div class="container">
    <h2 class="text-center fw-bold mb-5" data-aos="fade-down">
      HungerHub <span class="text-warning">In Numbers</span>
    </h2>
    <div class="row text-center g-4">
      
      <!-- Stat 1 -->
      <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="100">
        <div class="p-4 border rounded shadow-sm">
          <i class="fas fa-users fa-2x text-warning mb-2"></i>
          <h3 class="fw-bold">10K+</h3>
          <p>Happy Customers</p>
        </div>
      </div>

      <!-- Stat 2 -->
      <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="200">
        <div class="p-4 border rounded shadow-sm">
          <i class="fas fa-utensils fa-2x text-warning mb-2"></i>
          <h3 class="fw-bold">250+</h3>
          <p>Dishes Served</p>
        </div>
      </div>

      <!-- Stat 3 -->
      <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="300">
        <div class="p-4 border rounded shadow-sm">
          <i class="fas fa-star fa-2x text-warning mb-2"></i>
          <h3 class="fw-bold">4.8</h3>
          <p>Average Rating</p>
        </div>
      </div>

      <!-- Stat 4 -->
      <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="400">
        <div class="p-4 border rounded shadow-sm">
          <i class="fas fa-motorcycle fa-2x text-warning mb-2"></i>
          <h3 class="fw-bold">500+</h3>
          <p>Deliveries Daily</p>
        </div>
      </div>

    </div>
  </div>
</section>

      <!-- facts end -->
    <!-- work start -->
   <section class="py-5" id="how-it-works">
  <div class="container">
    <h2 class="text-center mb-5 fw-bold" data-aos="fade-down">How <span class="text-warning">HungerHub</span> Works</h2>
    <div class="row text-center g-4">
      
      <!-- Step 1: Order -->
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
        <div class="step-card p-4 shadow rounded h-100">
          <div class="step-icon mb-3">
            <i class="fas fa-utensils fa-3x text-warning"></i>
          </div>
          <h5 class="fw-bold">1. Choose & Order</h5>
          <p>Select your favorite dishes from our wide range of mouth-watering menus.</p>
        </div>
      </div>

      <!-- Step 2: Cook -->
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
        <div class="step-card p-4 shadow rounded h-100">
          <div class="step-icon mb-3">
            <i class="fas fa-concierge-bell fa-3x text-warning"></i>
          </div>
          <h5 class="fw-bold">2. We Cook Fresh</h5>
          <p>Our expert chefs prepare your order with the freshest ingredients.</p>
        </div>
      </div>

      <!-- Step 3: Deliver -->
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
        <div class="step-card p-4 shadow rounded h-100">
          <div class="step-icon mb-3">
            <i class="fas fa-motorcycle fa-3x text-warning"></i>
          </div>
          <h5 class="fw-bold">3. Safe Delivery</h5>
          <p>Your food is delivered hot & safe by our reliable delivery partners.</p>
        </div>
      </div>

    </div>
  </div>
</section>

  <!-- Contact Section Start -->
  <section class="py-5" id="contact">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold" data-aos="fade-down">Get in Touch with <span class="text-warning">HungerHub</span></h2>
      <div class="row contact-section align-items-center" data-aos="fade-up">
        <!-- Contact Form -->
        <div class="col-md-6">
          <form action="contact_process.php" method="POST">
            <div class="mb-3">
              <label class="form-label">Your Name</label>
              <input type="text" name="name" class="form-control" placeholder="John Doe" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control" placeholder="you@example.com" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Your Message</label>
              <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-warning w-100">Send Message</button>
          </form>
        </div>

        <!-- Contact Details -->
        <div class="col-md-6 ps-md-5 mt-5 mt-md-0">
          <div class="contact-info mb-4 d-flex align-items-center">
            <i class="fas fa-map-marker-alt me-2"></i>
            <p class="mb-0">Ranchi, Jharkhand, India</p>
          </div>
          <div class="contact-info mb-4 d-flex align-items-center">
            <i class="fas fa-envelope me-2"></i>
            <p class="mb-0">support@hungerhub.com</p>
          </div>
          <div class="contact-info mb-4 d-flex align-items-center">
            <i class="fas fa-phone-alt me-2"></i>
            <p class="mb-0">+91 9876543210</p>
          </div>
          <div class="rounded overflow-hidden mt-4">
            <iframe src="https://maps.google.com/maps?q=Ranchi&t=&z=13&ie=UTF8&iwloc=&output=embed"
              width="100%" height="220" style="border:0;" allowfullscreen loading="lazy"></iframe>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Contact Section End -->

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3">
    © 2025 HungerHub. All rights reserved.
  </footer>

  <!-- Bootstrap & AOS Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>

</html>
