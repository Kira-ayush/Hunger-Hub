<?php
session_start();
require 'db.php';

if (isset($_POST['submit'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: index.php#contact");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        header("Location: index.php#contact");
        exit();
    }

    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Thank you for your message! We'll get back to you soon.";
    } else {
        $_SESSION['error'] = "Sorry, there was an error sending your message. Please try again.";
    }

    header("Location: index.php#contact");
    exit();
} else {
    header("Location: index.php");
    exit();
}
