-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2025 at 07:15 PM
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
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `phone`, `address`, `items`, `total`, `created_at`, `user_id`) VALUES
(1, 'rahul', '7894563210', 'ratu', 'Chicken Manchurian (x1), Pav Bhaji (x1)', 230.00, '2025-07-18 07:41:35', 1);

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
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `phone`) VALUES
(1, 'rahul kumar', 'chhoturahul944@gmail.com', '$2y$10$fphMM9TNiNha1mdjXyBEsuf9tcdxPtXQgpjpOmMXMX0dmqTVHdq.G', '2025-07-18 07:39:21', '7894563210'),
(2, 'Ayush', 'abc@gmail.com', '$2y$10$.CzExDHIw.k77SzwaWPVMuXaQ.oAh4exQJer5ugTyLSGYAfvJ8zrK', '2025-09-20 15:45:13', '56466'),
(3, 'Munna', 'munna@gmail.com', '$2y$10$7pSjJ/S7di5G4ewRCelR/.SasEtLNeSm0Bo3.vbD4UsHN57GsuEzG', '2025-09-20 15:50:39', '123456789');

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
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
