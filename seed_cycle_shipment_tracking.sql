-- ============================================================================
-- SeedCycle Internal Shipment Tracking System
-- Migration: Run this against your `seed cycle` database
-- ============================================================================

USE `seed cycle`;

-- ============================================================================
-- SHIPMENTS TABLE (Create or update)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `shipments` (
  `id`                INT(11)      NOT NULL AUTO_INCREMENT,
  `order_id`          INT(11)      NOT NULL,
  `tracking_number`   VARCHAR(100) NOT NULL,
  `courier`           VARCHAR(100) NOT NULL DEFAULT 'SeedCycle Internal',
  `status`            VARCHAR(50)  NOT NULL DEFAULT 'pending',
  `estimated_delivery` DATE        DEFAULT NULL,
  `shipped_at`        DATETIME     DEFAULT NULL,
  `delivered_at`      DATETIME     DEFAULT NULL,
  `notes`             TEXT         DEFAULT NULL,
  `created_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tracking_number` (`tracking_number`),
  KEY `idx_order_id`  (`order_id`),
  KEY `idx_status`    (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add shipped_at and delivered_at columns if they don't exist (for existing installs)
ALTER TABLE `shipments`
  ADD COLUMN IF NOT EXISTS `shipped_at`   DATETIME DEFAULT NULL AFTER `estimated_delivery`,
  ADD COLUMN IF NOT EXISTS `delivered_at` DATETIME DEFAULT NULL AFTER `shipped_at`;

-- ============================================================================
-- SHIPMENT LOGS TABLE (Create or update)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `shipment_logs` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `shipment_id` INT(11)      NOT NULL,
  `status`      VARCHAR(50)  NOT NULL,
  `remarks`     TEXT         DEFAULT NULL,
  `notes`       TEXT         DEFAULT NULL,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipment_id` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- ORDER NOTIFICATIONS TABLE (for shipment status notifications to buyers)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `order_notifications` (
  `id`                INT(11)      NOT NULL AUTO_INCREMENT,
  `order_id`          INT(11)      NOT NULL,
  `user_id`           INT(11)      NOT NULL,
  `notification_type` VARCHAR(50)  NOT NULL DEFAULT 'shipment',
  `title`             VARCHAR(255) NOT NULL,
  `message`           TEXT         NOT NULL,
  `is_read`           TINYINT(1)   DEFAULT 0,
  `created_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id`  (`user_id`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_is_read`  (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Ensure orders table has municipality column (some installs may be missing it)
-- ============================================================================
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `municipality` VARCHAR(100) DEFAULT NULL AFTER `city`;

-- ============================================================================
-- END OF SHIPMENT TRACKING MIGRATION
-- ============================================================================
