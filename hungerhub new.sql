-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2025 at 08:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hungerhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `profile_pic`, `created_at`) VALUES
(2, 'Aayush Kumar', 'aayush.kr.gope@gmail.com', '$2y$10$e.2aDGkNLiaIdt76qb2QRusU610hlR4aV/7HCBtBfJv4YnZiOowf.', '../uploads/ADMIN_68789c49197ff9.49474001.png', '2025-07-17 06:46:33'),
(4, 'Anita Kumari', 'anita@gmail.com', '$2y$10$FVVky8i4TuCx.fhYw90Yu.tvkPQ4WifXf6mxu41wFz5OLuN5ChcMC', '../uploads/ADMIN_68789f6cd58d61.02855811.jpg', '2025-07-17 06:59:56'),
(5, 'Jyoti Kumari', 'jyoti@gmail.com', '$2y$10$MMeMwYIDPWB9WmGsIrd6Y.5Kg5bTLjmrBHExK9yoQQtmmne98a8Te', '../uploads/ADMIN_68789fc81481a7.36182904.png', '2025-07-17 07:01:28');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'John Doe', 'john@example.com', 'Great Service!', 'I love ordering from HungerHub. The food is always fresh and delivery is quick.', 'read', '2025-09-20 18:34:58', '2025-09-20 18:53:06'),
(2, NULL, 'Jane Smith', 'jane@example.com', 'Delivery Issue', 'My last order was delivered to the wrong address. Please help resolve this.', 'read', '2025-09-20 18:34:58', '2025-09-20 18:53:07'),
(3, NULL, 'Mike Johnson', 'mike@example.com', 'Menu Suggestion', 'Could you please add more vegetarian options to your menu?', 'read', '2025-09-20 18:34:58', '2025-09-20 18:34:58'),
(4, NULL, 'Sarah Wilson', 'sarah@example.com', 'Payment Problem', 'I was charged twice for my order #123. Please refund the duplicate charge.', 'read', '2025-09-20 18:34:58', '2025-09-20 18:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `main_category` varchar(20) NOT NULL,
  `sub_category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `price`, `description`, `image`, `created_at`, `main_category`, `sub_category`) VALUES
(2, 'Margherita Pizza', 179.00, 'Classic cheese pizza with tomato sauce', 'uploads/FOOD_6878a9dadad627.26675920.jpeg', '2025-07-17 07:44:26', 'Veg', 'Pizza'),
(3, 'Paneer Tikka Pizza', 229.00, 'Tandoori paneer chunks on a spicy crust', 'uploads/FOOD_6878ac2767e122.32918694.jpeg', '2025-07-17 07:54:15', 'Veg', 'Pizza'),
(4, 'Veg Biryani', 159.00, 'Fragrant rice with vegetables and spices', 'uploads/FOOD_6878acd31fd441.45362017.jpg', '2025-07-17 07:57:07', 'Veg', 'Biryani'),
(5, 'Masala Dosa', 99.00, 'Crispy dosa stuffed with spicy mashed potato', 'uploads/FOOD_6878ad2abe0e18.35318970.jpg', '2025-07-17 07:58:34', 'Veg', 'South Indian'),
(6, 'Spring Rolls', 89.00, 'Crispy rolls filled with veggie mix', 'uploads/FOOD_6878ada1d4e361.51861149.jpg', '2025-07-17 08:00:33', 'Veg', 'Chinese'),
(7, 'Gulab Jamun', 59.00, 'Sweet milk balls soaked in sugar syrup', 'uploads/FOOD_6878ae468e7b60.06308617.jpg', '2025-07-17 08:03:18', 'Veg', 'Desserts'),
(8, 'Aloo Tikki Chaat', 79.00, 'Spicy potato patties topped with chutneys', 'uploads/FOOD_6878af19602ac3.30739324.png', '2025-07-17 08:06:49', 'Veg', 'Street Food'),
(9, 'Mixed Veg Thali', 149.00, 'Complete meal with rice, chapati &amp; sabzi', 'uploads/FOOD_6878b0090b4ce8.27923697.png', '2025-07-17 08:10:49', 'Veg', 'Thali'),
(10, 'Chicken Biryani', 199.00, 'Basmati rice with chicken &amp; aromatic spices', 'uploads/FOOD_6878b1fa7b8c57.55417892.jpg', '2025-07-17 08:19:06', 'Non-Veg', 'Biryani'),
(11, 'Egg Fried Rice', 139.00, 'Stir-fried rice with egg and veggies', 'uploads/FOOD_6878b2c5eadcd3.14874529.jpeg', '2025-07-17 08:22:29', 'Non-Veg', 'Chinese'),
(12, 'Chicken Lollipop', 169.00, 'Deep-fried chicken wings in spicy coating', 'uploads/FOOD_6878b37461e4d1.71571377.jpg', '2025-07-17 08:25:24', 'Non-Veg', 'Snacks'),
(13, 'Butter Chicken', 249.00, 'Creamy tomato-based chicken curry', 'uploads/FOOD_6878b3fbadec79.56463848.jpg', '2025-07-17 08:27:39', 'Non-Veg', 'Thali'),
(14, 'Fish Fry', 189.00, 'Crispy marinated fish, deep fried', 'uploads/FOOD_6878b50ea59ae9.05624856.jpg', '2025-07-17 08:32:14', 'Non-Veg', 'Street Food'),
(15, 'Chole Bhature', 119.00, 'Spicy chole served with puffed bhature', 'uploads/FOOD_6878b592d130c2.26483780.png', '2025-07-17 08:34:26', 'Veg', 'Street Food'),
(16, 'Chicken Kebab Roll', 149.00, 'Grilled chicken in soft roll with sauces', 'uploads/FOOD_6878b5d63185c0.30733193.jpg', '2025-07-17 08:35:34', 'Non-Veg', 'Snacks'),
(17, 'Mixed Fruit Salad', 89.00, 'Healthy mix of seasonal fruits', 'uploads/FOOD_6878b6572f18a6.24799902.jpg', '2025-07-17 08:37:43', 'Veg', 'Salads'),
(18, 'Idli Sambar', 69.00, 'Steamed rice cakes served with sambar', 'uploads/FOOD_6878b6863fc019.40364017.jpg', '2025-07-17 08:38:30', 'Veg', 'South Indian'),
(19, 'Chicken Tikka Pizza', 220.00, 'Spicy chicken tikka with onion and bell pepper.', 'uploads/FOOD_6879e38f92ae32.89073099.jpg', '2025-07-18 06:02:55', 'Non-Veg', 'Pizza'),
(20, 'Paneer Pakora', 90.00, 'Deep-fried paneer fritters served with chutney.', 'uploads/FOOD_6879e4887ddaf0.61372277.jpg', '2025-07-18 06:07:04', 'Veg', 'Snacks'),
(21, 'Chicken 65', 130.00, 'Crispy, spicy South Indian chicken starter.', 'uploads/FOOD_6879e5706faf27.94084407.jpg', '2025-07-18 06:10:56', 'Non-Veg', 'Snacks'),
(22, 'Veg Chowmein', 110.00, 'Stir-fried noodles with veggies and sauces.', 'uploads/FOOD_6879e5ece1aaa1.92601971.jpg', '2025-07-18 06:13:00', 'Veg', 'Chinese'),
(23, 'Chicken Manchurian', 140.00, 'Spicy Indo-Chinese chicken balls in thick gravy.', 'uploads/FOOD_6879e68ae7cbc3.10340276.jpg', '2025-07-18 06:15:38', 'Non-Veg', 'Chinese'),
(24, 'Non-Veg Deluxe Thali', 180.00, 'Chicken curry, rice, dal, salad, and roti.', 'uploads/FOOD_6879e9898fa501.61481792.png', '2025-07-18 06:28:25', 'Non-Veg', 'Thali'),
(25, 'Pav Bhaji', 90.00, 'Mashed spiced veggies served with buttered pav.', 'uploads/FOOD_6879eaa6bd89e6.50885377.jpg', '2025-07-18 06:33:10', 'Veg', 'Street Food');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Confirmed','Preparing','Ready','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `payment_method` enum('COD','Razorpay','PayPal','UPI','Card') DEFAULT 'COD',
  `payment_status` enum('Pending','Processing','Completed','Failed','Refunded') DEFAULT 'Pending',
  `payment_id` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'INR',
  `payment_date` datetime DEFAULT NULL,
  `transaction_fee` decimal(10,2) DEFAULT 0.00,
  `payment_gateway_response` text DEFAULT NULL,
  `estimated_delivery` datetime DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `phone`, `address`, `items`, `total`, `status`, `payment_method`, `payment_status`, `payment_id`, `payment_amount`, `currency`, `payment_date`, `transaction_fee`, `payment_gateway_response`, `estimated_delivery`, `admin_notes`, `created_at`, `user_id`) VALUES
(1, 'rahul', '7894563210', 'ratu', 'Chicken Manchurian (x1), Pav Bhaji (x1)', 230.00, 'Delivered', 'COD', 'Pending', NULL, NULL, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-07-18 07:41:35', 1),
(2, 'Ayush Kumar', '9110160470', '8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)', 'Non-Veg Deluxe Thali (x1)', 180.00, 'Pending', 'UPI', 'Pending', NULL, 180.00, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:49:24', 4),
(3, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x2, Veg Biryani x1', 1154.00, 'Pending', 'COD', 'Pending', NULL, 1154.00, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:51:36', 5),
(4, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x2, Veg Biryani x1', 1181.23, 'Pending', 'Razorpay', 'Pending', NULL, 1181.23, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:51:36', 5),
(5, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x3', 1243.00, 'Pending', 'COD', 'Pending', NULL, 1243.00, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:11', 5),
(6, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x3', 1272.33, 'Pending', 'Razorpay', 'Pending', NULL, 1272.33, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:11', 5),
(7, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x2, Paneer Tikka Pizza x3, Veg Biryani x1', 1204.00, 'Pending', 'COD', 'Pending', NULL, 1204.00, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:22', 5),
(8, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x2, Paneer Tikka Pizza x3, Veg Biryani x1', 1232.41, 'Pending', 'Razorpay', 'Pending', NULL, 1232.41, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:22', 5),
(9, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x2', 1084.00, 'Pending', 'COD', 'Pending', NULL, 1084.00, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:45', 5),
(10, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x2', 1109.58, 'Pending', 'Razorpay', 'Pending', NULL, 1109.58, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:45', 5),
(11, 'Test User', '9999999999', 'Test Address, Test City, 123456', 'Margherita Pizza x3, Paneer Tikka Pizza x1, Veg Biryani x2', 1115.44, 'Pending', 'PayPal', 'Pending', NULL, 1115.44, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:52:45', 5),
(12, 'Ayush Kumar', '9110160470', '8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)', 'Non-Veg Deluxe Thali (x1)', 180.00, 'Pending', 'UPI', 'Pending', NULL, 180.00, 'INR', NULL, 0.00, NULL, NULL, NULL, '2025-09-20 17:58:18', 4),
(13, 'Sumit Kumar Yadav', '9110160470', '8A ,VILL PO - HUNDRU ,PS- AIRPORT(DORANDA)', 'Non-Veg Deluxe Thali (x1)', 180.00, 'Confirmed', 'COD', 'Pending', NULL, 180.00, 'INR', NULL, 0.00, NULL, NULL, '', '2025-09-20 17:58:24', 4),
(14, 'Aayush Kumar', '9110160470', 'Hinoo', 'Veg Chowmein (x1), Pav Bhaji (x1)', 200.00, 'Delivered', 'UPI', 'Pending', NULL, 200.00, 'INR', NULL, 0.00, NULL, NULL, '', '2025-09-20 18:22:12', 4),
(15, 'Aayush Kumar', '9110160470', 'Hinoo', 'Veg Chowmein (x1), Pav Bhaji (x1)', 200.00, 'Delivered', 'UPI', 'Pending', NULL, 200.00, 'INR', NULL, 0.00, NULL, NULL, 'ddd', '2025-09-20 18:22:37', 4);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `item_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_gateway` enum('Razorpay','PayPal','UPI','Card','Wallet') NOT NULL,
  `gateway_payment_id` varchar(255) NOT NULL,
  `gateway_order_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'INR',
  `status` enum('Created','Authorized','Captured','Failed','Cancelled','Refunded') NOT NULL,
  `gateway_response` text DEFAULT NULL,
  `transaction_fee` decimal(10,2) DEFAULT 0.00,
  `net_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `payment_gateway`, `gateway_payment_id`, `gateway_order_id`, `amount`, `currency`, `status`, `gateway_response`, `transaction_fee`, `net_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '', 'COD_1_1758390464', NULL, 230.00, 'INR', 'Captured', NULL, 0.00, 230.00, '2025-07-18 07:41:35', '2025-09-20 17:47:44'),
(2, 10, 5, 'Razorpay', 'test_RAZORPAY_1758390765', NULL, 1109.58, 'INR', 'Captured', '{\"test_payment\":true,\"method\":\"RAZORPAY\",\"amount\":1109.5824}', 0.00, 0.00, '2025-09-20 17:52:45', '2025-09-20 17:52:45'),
(3, 11, 5, 'PayPal', 'test_PAYPAL_1758390765', NULL, 1115.44, 'INR', 'Captured', '{\"test_payment\":true,\"method\":\"PAYPAL\",\"amount\":1115.436}', 0.00, 0.00, '2025-09-20 17:52:45', '2025-09-20 17:52:45');

-- --------------------------------------------------------

--
-- Stand-in structure for view `payment_analytics`
-- (See below for the actual view)
--
CREATE TABLE `payment_analytics` (
`payment_date` date
,`payment_gateway` enum('Razorpay','PayPal','UPI','Card','Wallet')
,`total_transactions` bigint(21)
,`total_amount` decimal(32,2)
,`total_fees` decimal(32,2)
,`net_revenue` decimal(32,2)
,`avg_transaction_amount` decimal(14,6)
,`successful_payments` bigint(21)
,`failed_payments` bigint(21)
,`success_rate` decimal(28,5)
);

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `event_type` enum('Payment_Created','Payment_Authorized','Payment_Captured','Payment_Failed','Webhook_Received','Refund_Initiated','Refund_Completed') NOT NULL,
  `gateway` varchar(50) NOT NULL,
  `gateway_event_id` varchar(255) DEFAULT NULL,
  `request_data` text DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `gateway_refund_id` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Processing','Completed','Failed') DEFAULT 'Pending',
  `gateway_response` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `phone`, `address`, `last_login`) VALUES
(1, 'rahul kumar', 'chhoturahul944@gmail.com', '$2y$10$fphMM9TNiNha1mdjXyBEsuf9tcdxPtXQgpjpOmMXMX0dmqTVHdq.G', '2025-07-18 07:39:21', '7894563210', NULL, NULL),
(2, 'Ayush', 'abc@gmail.com', '$2y$10$.CzExDHIw.k77SzwaWPVMuXaQ.oAh4exQJer5ugTyLSGYAfvJ8zrK', '2025-09-20 15:45:13', '56466', NULL, NULL),
(3, 'Munna', 'munna@gmail.com', '$2y$10$7pSjJ/S7di5G4ewRCelR/.SasEtLNeSm0Bo3.vbD4UsHN57GsuEzG', '2025-09-20 15:50:39', '123456789', NULL, NULL),
(4, 'Aayush Kumar', 'aayush.kr.gope@gmail.com', '$2y$10$NN5sgUB9ZTfEeFtqSKxH/.5vHBwVGNQHAVSmV8pT74mgAWZmlXwSS', '2025-09-20 17:21:49', '9110160470', NULL, NULL),
(5, 'Test User', 'testuser@example.com', '$2y$10$Wo43PFZx/GdQbIV3A2YZ4u9fidrfFGJHS0jo2oqxu5IZcxLB.x.g.', '2025-09-20 17:51:36', '9999999999', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_payment_methods`
--

CREATE TABLE `user_payment_methods` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gateway` enum('Razorpay','PayPal') NOT NULL,
  `gateway_customer_id` varchar(255) NOT NULL,
  `payment_method_id` varchar(255) NOT NULL,
  `method_type` enum('Card','UPI','Netbanking','Wallet') NOT NULL,
  `last_four` varchar(4) DEFAULT NULL,
  `card_brand` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `payment_analytics`
--
DROP TABLE IF EXISTS `payment_analytics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `payment_analytics`  AS SELECT cast(`p`.`created_at` as date) AS `payment_date`, `p`.`payment_gateway` AS `payment_gateway`, count(0) AS `total_transactions`, sum(`p`.`amount`) AS `total_amount`, sum(`p`.`transaction_fee`) AS `total_fees`, sum(`p`.`net_amount`) AS `net_revenue`, avg(`p`.`amount`) AS `avg_transaction_amount`, count(case when `p`.`status` = 'Captured' then 1 end) AS `successful_payments`, count(case when `p`.`status` = 'Failed' then 1 end) AS `failed_payments`, count(case when `p`.`status` = 'Captured' then 1 end) * 100.0 / count(0) AS `success_rate` FROM `payments` AS `p` GROUP BY cast(`p`.`created_at` as date), `p`.`payment_gateway` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `payment_method` (`payment_method`),
  ADD KEY `payment_date` (`payment_date`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gateway_payment_id` (`gateway_payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `event_type` (`event_type`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_payment_methods`
--
ALTER TABLE `user_payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `gateway_customer_id` (`gateway_customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_payment_methods`
--
ALTER TABLE `user_payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_payment_methods`
--
ALTER TABLE `user_payment_methods`
  ADD CONSTRAINT `user_payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
