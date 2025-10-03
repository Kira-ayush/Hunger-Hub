<?php
session_start();
require 'db.php';

$success_message = '';
$error_message = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // In a real application, you would save this to a database or send an email
        // For now, we'll just show a success message
        $success_message = 'Thank you for your message! We will get back to you within 24 hours.';

        // Clear form data on success
        $name = $email = $phone = $subject = $message = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Us - HungerHub</title>
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
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.8), rgba(255, 193, 7, 0.8)), url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?w=1920&h=600&fit=crop');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
        }

        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border: none;
        }

        .contact-card:hover {
            transform: translateY(-5px);
        }

        .contact-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 20px;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        .btn-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #000;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #000;
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .hours-card {
            background: linear-gradient(135deg, #20c997, #0d6efd);
            color: white;
            border-radius: 15px;
            padding: 30px;
        }

        .hours-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hours-item:last-child {
            border-bottom: none;
        }

        .faq-item {
            background: white;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .faq-header {
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-header:hover {
            background: #ffc107;
            color: #000;
        }

        .alert-success {
            background-color: #d1edff;
            border-color: #b6d7ff;
            color: #0c5460;
            border-radius: 10px;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div data-aos="fade-up">
                <h1 class="display-4 fw-bold mb-4">Get In Touch</h1>
                <p class="lead mb-4">We'd love to hear from you! Reach out with questions, feedback, or just to say hello.</p>
                <p class="fs-5">Our friendly team is here to help you 24/7</p>
            </div>
        </div>
    </section>

    <!-- Contact Cards Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-card text-center">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Call Us</h4>
                        <p class="text-muted mb-3">Speak directly with our customer service team</p>
                        <p class="fs-5 fw-bold text-primary">+1 (555) 123-4567</p>
                        <p class="text-muted">Available 24/7</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-card text-center">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Email Us</h4>
                        <p class="text-muted mb-3">Send us a message and we'll respond quickly</p>
                        <p class="fs-5 fw-bold text-primary">info@hungerhub.com</p>
                        <p class="text-muted">Response within 2 hours</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-card text-center">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Visit Us</h4>
                        <p class="text-muted mb-3">Come see us at our main office</p>
                        <p class="fs-6 fw-bold">123 Food Street<br>Delicious City, DC 12345</p>
                        <p class="text-muted">Mon-Fri, 9AM-6PM</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-card" data-aos="fade-up">
                        <div class="text-center mb-4">
                            <h2 class="display-6 fw-bold mb-3">Send Us a Message</h2>
                            <p class="text-muted">Fill out the form below and we'll get back to you as soon as possible</p>
                        </div>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= htmlspecialchars($success_message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error_message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="subject" class="form-label">Subject *</label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="">Choose a subject...</option>
                                        <option value="General Inquiry" <?= ($subject ?? '') === 'General Inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                                        <option value="Order Issue" <?= ($subject ?? '') === 'Order Issue' ? 'selected' : '' ?>>Order Issue</option>
                                        <option value="Restaurant Partnership" <?= ($subject ?? '') === 'Restaurant Partnership' ? 'selected' : '' ?>>Restaurant Partnership</option>
                                        <option value="Technical Support" <?= ($subject ?? '') === 'Technical Support' ? 'selected' : '' ?>>Technical Support</option>
                                        <option value="Feedback" <?= ($subject ?? '') === 'Feedback' ? 'selected' : '' ?>>Feedback</option>
                                        <option value="Other" <?= ($subject ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Message *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tell us how we can help you..." required><?= htmlspecialchars($message ?? '') ?></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map and Hours Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8" data-aos="fade-right">
                    <h3 class="fw-bold mb-4">Find Us Here</h3>
                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387191.036!2d-74.309!3d40.697!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sus!4v1635959015"
                            width="100%"
                            height="400"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-left">
                    <div class="hours-card">
                        <h3 class="fw-bold mb-4">
                            <i class="fas fa-clock me-2"></i>Business Hours
                        </h3>
                        <div class="hours-item">
                            <span class="fw-bold">Monday - Friday</span>
                            <span>9:00 AM - 11:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="fw-bold">Saturday</span>
                            <span>10:00 AM - 12:00 AM</span>
                        </div>
                        <div class="hours-item">
                            <span class="fw-bold">Sunday</span>
                            <span>10:00 AM - 10:00 PM</span>
                        </div>
                        <div class="mt-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.2);">
                            <p class="mb-2"><strong>Delivery Hours:</strong></p>
                            <p class="mb-0">24/7 - We never sleep so you don't have to!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center mb-5" data-aos="fade-up">
                        <h2 class="display-6 fw-bold mb-3">Frequently Asked Questions</h2>
                        <p class="text-muted">Quick answers to common questions</p>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="100">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item faq-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button faq-header" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        <strong>What are your delivery hours?</strong>
                                    </button>
                                </h3>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We offer 24/7 delivery service! Our kitchen partners work around the clock to satisfy your cravings anytime, day or night.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed faq-header" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        <strong>What is your delivery fee?</strong>
                                    </button>
                                </h3>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Delivery fees start at $2.99 and may vary based on distance and demand. Free delivery is available for orders over $25!
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed faq-header" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        <strong>How long does delivery usually take?</strong>
                                    </button>
                                </h3>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Most deliveries arrive within 30-45 minutes. During peak hours or busy weather, it might take up to 60 minutes.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed faq-header" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        <strong>What payment methods do you accept?</strong>
                                    </button>
                                </h3>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We accept UPI payments and Cash on Delivery (COD) for your convenience. More payment options coming soon!
                                    </div>
                                </div>
                            </div>
                        </div>
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