-- Database Updates for HungerHub Order Status Management
-- Execute these queries in phpMyAdmin or MySQL command line

-- 1. Add status column to orders table
ALTER TABLE `orders` 
ADD COLUMN `status` ENUM('Pending', 'Confirmed', 'Preparing', 'Ready', 'Out for Delivery', 'Delivered', 'Cancelled') 
NOT NULL DEFAULT 'Pending' 
AFTER `total`;

-- 2. Add estimated delivery time column
ALTER TABLE `orders` 
ADD COLUMN `estimated_delivery` DATETIME NULL 
AFTER `status`;

-- 3. Add order notes for admin
ALTER TABLE `orders` 
ADD COLUMN `admin_notes` TEXT NULL 
AFTER `estimated_delivery`;

-- 4. Update existing orders to have 'Delivered' status (optional)
UPDATE `orders` SET `status` = 'Delivered' WHERE `created_at` < NOW();

-- 5. Create messages table for contact form
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Create order_items table for better order tracking (optional enhancement)
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `item_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `menu_item_id` (`menu_item_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;