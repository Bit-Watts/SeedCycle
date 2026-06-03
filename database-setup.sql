-- ============================================================================
-- SeedCycle Database Setup
-- ============================================================================
-- This file contains all necessary tables for the SeedCycle application
-- Run this file to set up a fresh database
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `seed cycle` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `seed cycle`;

-- ============================================================================
-- USERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `profile_image` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INVENTORY TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `stock_quantity` INT(11) DEFAULT 0,
  `price` DECIMAL(10,2) DEFAULT 0.00,
  `planting_months` VARCHAR(255) DEFAULT NULL,
  `days_to_harvest` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SEED LISTINGS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `seed_listings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `inventory_id` INT(11) NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `image_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_inventory_id` (`inventory_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ORDERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'processing', 'delivered', 'cancelled') DEFAULT 'pending',
  `shipping_status` VARCHAR(50) DEFAULT 'pending',
  `delivery_method` VARCHAR(50) DEFAULT 'standard',
  `street_address` VARCHAR(255) DEFAULT NULL,
  `barangay` VARCHAR(100) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `province` VARCHAR(100) DEFAULT NULL,
  `zip_code` VARCHAR(20) DEFAULT NULL,
  `payment_method` VARCHAR(20) DEFAULT 'cod',
  `payment_status` VARCHAR(20) DEFAULT 'pending',
  `payment_reference` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ORDER ITEMS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `inventory_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_inventory_id` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- CART TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `inventory_id` INT(11) NOT NULL,
  `quantity` INT(11) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_inventory_id` (`inventory_id`),
  UNIQUE KEY `unique_user_inventory` (`user_id`, `inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SHIPMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `shipments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `courier` VARCHAR(100) NOT NULL,
  `tracking_number` VARCHAR(100) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'pending',
  `estimated_delivery` DATE DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SHIPMENT LOGS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `shipment_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `shipment_id` INT(11) NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipment_id` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- REVIEWS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `inventory_id` INT(11) NOT NULL,
  `rating` INT(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
  `comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_inventory_id` (`inventory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- REPORTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `reporter_id` INT(11) NOT NULL,
  `reported_user_id` INT(11) NOT NULL,
  `listing_id` INT(11) DEFAULT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reporter_id` (`reporter_id`),
  KEY `idx_reported_user_id` (`reported_user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PAYMENT TRANSACTIONS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `payment_status` VARCHAR(50) NOT NULL,
  `payment_reference` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TWO FACTOR AUTHENTICATION TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `two_factor` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `code` VARCHAR(6) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `verified` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE DATA (Optional - Remove if not needed)
-- ============================================================================

-- Insert default admin user (password: admin123)
INSERT INTO `users` (`first_name`, `last_name`, `username`, `email`, `password_hash`, `role`) 
VALUES ('Admin', 'User', 'admin', 'admin@seedcycle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================================================
-- END OF DATABASE SETUP
-- ============================================================================
