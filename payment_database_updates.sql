-- Payment Integration Database Updates for HungerHub
-- Execute these queries in phpMyAdmin or MySQL command line

-- 1. Add payment-related columns to orders table
ALTER TABLE `orders` 
ADD COLUMN `payment_method` ENUM('COD', 'Razorpay', 'PayPal', 'UPI', 'Card') DEFAULT 'COD' AFTER `status`,
ADD COLUMN `payment_status` ENUM('Pending', 'Processing', 'Completed', 'Failed', 'Refunded') DEFAULT 'Pending' AFTER `payment_method`,
ADD COLUMN `payment_id` VARCHAR(255) NULL AFTER `payment_status`,
ADD COLUMN `payment_amount` DECIMAL(10,2) NULL AFTER `payment_id`,
ADD COLUMN `currency` VARCHAR(3) DEFAULT 'INR' AFTER `payment_amount`,
ADD COLUMN `payment_date` DATETIME NULL AFTER `currency`,
ADD COLUMN `transaction_fee` DECIMAL(10,2) DEFAULT 0.00 AFTER `payment_date`,
ADD COLUMN `payment_gateway_response` TEXT NULL AFTER `transaction_fee`;

-- 2. Create payments table for detailed transaction tracking
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_gateway` ENUM('Razorpay', 'PayPal', 'UPI', 'Card', 'Wallet') NOT NULL,
  `gateway_payment_id` VARCHAR(255) NOT NULL,
  `gateway_order_id` VARCHAR(255) NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(3) DEFAULT 'INR',
  `status` ENUM('Created', 'Authorized', 'Captured', 'Failed', 'Cancelled', 'Refunded') NOT NULL,
  `gateway_response` TEXT NULL,
  `transaction_fee` DECIMAL(10,2) DEFAULT 0.00,
  `net_amount` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gateway_payment_id` (`gateway_payment_id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Create payment_logs table for debugging and audit trail
CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NULL,
  `order_id` int(11) NOT NULL,
  `event_type` ENUM('Payment_Created', 'Payment_Authorized', 'Payment_Captured', 'Payment_Failed', 'Webhook_Received', 'Refund_Initiated', 'Refund_Completed') NOT NULL,
  `gateway` VARCHAR(50) NOT NULL,
  `gateway_event_id` VARCHAR(255) NULL,
  `request_data` TEXT NULL,
  `response_data` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `order_id` (`order_id`),
  KEY `event_type` (`event_type`),
  CONSTRAINT `payment_logs_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_logs_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Create refunds table
CREATE TABLE `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `refund_amount` DECIMAL(10,2) NOT NULL,
  `reason` TEXT NULL,
  `gateway_refund_id` VARCHAR(255) NULL,
  `status` ENUM('Pending', 'Processing', 'Completed', 'Failed') DEFAULT 'Pending',
  `gateway_response` TEXT NULL,
  `processed_by` int(11) NULL, -- admin_id who processed the refund
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `payment_id` (`payment_id`),
  KEY `order_id` (`order_id`),
  KEY `processed_by` (`processed_by`),
  CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `refunds_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Create payment_methods table for storing user's saved payment methods
CREATE TABLE `user_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gateway` ENUM('Razorpay', 'PayPal') NOT NULL,
  `gateway_customer_id` VARCHAR(255) NOT NULL,
  `payment_method_id` VARCHAR(255) NOT NULL,
  `method_type` ENUM('Card', 'UPI', 'Netbanking', 'Wallet') NOT NULL,
  `last_four` VARCHAR(4) NULL, -- for cards
  `card_brand` VARCHAR(20) NULL, -- Visa, Mastercard, etc.
  `is_default` BOOLEAN DEFAULT FALSE,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `gateway_customer_id` (`gateway_customer_id`),
  CONSTRAINT `user_payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Add indexes for better performance
ALTER TABLE `orders` ADD INDEX `payment_status` (`payment_status`);
ALTER TABLE `orders` ADD INDEX `payment_method` (`payment_method`);
ALTER TABLE `orders` ADD INDEX `payment_date` (`payment_date`);

-- 7. Update existing orders to have default payment values
UPDATE `orders` SET 
  `payment_method` = 'COD',
  `payment_status` = 'Completed',
  `payment_amount` = `total`,
  `currency` = 'INR',
  `payment_date` = `created_at`
WHERE `payment_method` IS NULL;

-- 8. Create view for payment analytics
CREATE VIEW `payment_analytics` AS
SELECT 
  DATE(p.created_at) as payment_date,
  p.payment_gateway,
  COUNT(*) as total_transactions,
  SUM(p.amount) as total_amount,
  SUM(p.transaction_fee) as total_fees,
  SUM(p.net_amount) as net_revenue,
  AVG(p.amount) as avg_transaction_amount,
  COUNT(CASE WHEN p.status = 'Captured' THEN 1 END) as successful_payments,
  COUNT(CASE WHEN p.status = 'Failed' THEN 1 END) as failed_payments,
  (COUNT(CASE WHEN p.status = 'Captured' THEN 1 END) * 100.0 / COUNT(*)) as success_rate
FROM payments p
GROUP BY DATE(p.created_at), p.payment_gateway;

-- 9. Insert sample payment gateway configuration (optional)
INSERT INTO `payments` (`order_id`, `user_id`, `payment_gateway`, `gateway_payment_id`, `amount`, `currency`, `status`, `net_amount`, `created_at`) 
SELECT 
  o.id,
  o.user_id,
  'COD' as payment_gateway,
  CONCAT('COD_', o.id, '_', UNIX_TIMESTAMP()) as gateway_payment_id,
  o.total,
  'INR',
  'Captured',
  o.total,
  o.created_at
FROM orders o 
WHERE NOT EXISTS (SELECT 1 FROM payments p WHERE p.order_id = o.id);

-- Verification queries to check if updates were successful
-- SELECT COUNT(*) as orders_with_payment_fields FROM orders WHERE payment_method IS NOT NULL;
-- SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'hungerhub' AND TABLE_NAME IN ('payments', 'payment_logs', 'refunds', 'user_payment_methods');
-- DESCRIBE orders;
-- DESCRIBE payments;