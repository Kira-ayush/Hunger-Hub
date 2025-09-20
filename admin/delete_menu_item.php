<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require '../db.php';

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid item ID.";
    header("Location: menu_items.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch the image path to delete the file
$stmt = $conn->prepare("SELECT image FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    $_SESSION['error'] = "Item not found.";
    header("Location: menu_items.php");
    exit();
}

// Delete image file
$image_path = "../" . $item['image'];
if (file_exists($image_path)) {
    unlink($image_path); // Remove the image file
}

// Delete from DB
$stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $_SESSION['success'] = "Menu item deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete menu item.";
}

header("Location: menu_items.php");
exit();
