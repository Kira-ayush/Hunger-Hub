<?php
session_start();
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About Us - HungerHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css" />
    <link href="images/logo.png" rel="icon" type="image/png">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.8), rgba(108, 117, 125, 0.8)), url('images/about_us.png');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #ffc107;
        }

        .team-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }

        .team-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
        }

        .mission-card {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            border-radius: 15px;
            padding: 40px;
        }

        .vision-card {
            background: linear-gradient(135deg, #20c997, #0d6efd);
            color: white;
            border-radius: 15px;
            padding: 40px;
        }

        .values-card {
            background: white;
            border: 2px solid #ffc107;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .values-card:hover {
            background: #ffc107;
            color: white;
        }

        .values-icon {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 20px;
        }

        .values-card:hover .values-icon {
            color: white;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div data-aos="fade-up">
                <h1 class="display-4 fw-bold mb-4">About HungerHub</h1>
                <p class="lead mb-4">Connecting food lovers with their favorite flavors since 2020</p>
                <p class="fs-5">We're passionate about bringing delicious, quality food right to your doorstep with just a few clicks.</p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card text-center">
                        <div class="stat-number">500+</div>
                        <h5 class="fw-bold">Happy Customers</h5>
                        <p class="text-muted">Satisfied customers served daily</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card text-center">
                        <div class="stat-number">50+</div>
                        <h5 class="fw-bold">Menu Items</h5>
                        <p class="text-muted">Delicious dishes to choose from</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card text-center">
                        <div class="stat-number">15+</div>
                        <h5 class="fw-bold">Partner Restaurants</h5>
                        <p class="text-muted">Quality restaurant partnerships</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card text-center">
                        <div class="stat-number">24/7</div>
                        <h5 class="fw-bold">Service Hours</h5>
                        <p class="text-muted">Always available for you</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center" data-aos="fade-up">
                    <h2 class="display-5 fw-bold mb-3">Our Purpose</h2>
                    <p class="lead text-muted">What drives us to serve you better every day</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="mission-card">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-bullseye fa-3x me-3"></i>
                            <h3 class="fw-bold mb-0">Our Mission</h3>
                        </div>
                        <p class="fs-5 mb-0">To revolutionize food delivery by connecting people with their favorite local restaurants, ensuring fresh, quality meals are delivered quickly and efficiently to enhance dining experiences at home.</p>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="vision-card">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-eye fa-3x me-3"></i>
                            <h3 class="fw-bold mb-0">Our Vision</h3>
                        </div>
                        <p class="fs-5 mb-0">To become the leading food delivery platform that brings communities together through exceptional culinary experiences, supporting local businesses while satisfying every craving with convenience and care.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center" data-aos="fade-up">
                    <h2 class="display-5 fw-bold mb-3">Our Values</h2>
                    <p class="lead text-muted">The principles that guide everything we do</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="values-card">
                        <i class="fas fa-heart values-icon"></i>
                        <h4 class="fw-bold mb-3">Quality First</h4>
                        <p>We partner only with restaurants that share our commitment to fresh, high-quality ingredients and exceptional preparation.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="values-card">
                        <i class="fas fa-bolt values-icon"></i>
                        <h4 class="fw-bold mb-3">Speed & Reliability</h4>
                        <p>Fast delivery times and dependable service are at the core of our promise to bring you hot, fresh meals when you want them.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="values-card">
                        <i class="fas fa-users values-icon"></i>
                        <h4 class="fw-bold mb-3">Community Focus</h4>
                        <p>Supporting local restaurants and creating jobs in our community while bringing people together through great food experiences.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="values-card">
                        <i class="fas fa-shield-alt values-icon"></i>
                        <h4 class="fw-bold mb-3">Safety & Hygiene</h4>
                        <p>Maintaining the highest standards of food safety and hygiene throughout our entire delivery process for your peace of mind.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="values-card">
                        <i class="fas fa-smile values-icon"></i>
                        <h4 class="fw-bold mb-3">Customer Satisfaction</h4>
                        <p>Your happiness is our success. We go above and beyond to ensure every order exceeds your expectations.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="values-card">
                        <i class="fas fa-leaf values-icon"></i>
                        <h4 class="fw-bold mb-3">Sustainability</h4>
                        <p>Committed to eco-friendly packaging and sustainable practices that protect our environment for future generations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center" data-aos="fade-up">
                    <h2 class="display-5 fw-bold mb-3">Meet Our Team</h2>
                    <p class="lead text-muted">The passionate people behind HungerHub</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-card text-center p-4">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop&crop=face" alt="John Smith" class="team-img mb-3">
                        <h4 class="fw-bold">John Smith</h4>
                        <p class="text-warning fw-bold">CEO & Founder</p>
                        <p class="text-muted">Passionate about connecting people with great food. 10+ years in food industry.</p>
                        <div class="social-links">
                            <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-facebook"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-card text-center p-4">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=300&h=300&fit=crop&crop=face" alt="Sarah Johnson" class="team-img mb-3">
                        <h4 class="fw-bold">Sarah Johnson</h4>
                        <p class="text-warning fw-bold">Head of Operations</p>
                        <p class="text-muted">Ensures smooth operations and exceptional customer service across all locations.</p>
                        <div class="social-links">
                            <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-danger"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-card text-center p-4">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face" alt="Mike Chen" class="team-img mb-3">
                        <h4 class="fw-bold">Mike Chen</h4>
                        <p class="text-warning fw-bold">Head Chef Partner</p>
                        <p class="text-muted">Culinary expert who ensures all our partner restaurants meet our quality standards.</p>
                        <div class="social-links">
                            <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-danger me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-facebook"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="py-5 bg-warning">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center" data-aos="fade-up">
                    <h2 class="display-5 fw-bold text-dark mb-4">Ready to Experience HungerHub?</h2>
                    <p class="lead text-dark mb-4">Join thousands of satisfied customers who trust us for their daily meals</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="menu.php" class="btn btn-dark btn-lg px-4">
                            <i class="fas fa-utensils me-2"></i>Browse Menu
                        </a>
                        <a href="user/register.php" class="btn btn-outline-dark btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>Sign Up Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap & AOS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>

</html>