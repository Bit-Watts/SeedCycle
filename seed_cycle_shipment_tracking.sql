-- SeedCycle: Shipment Tracking Enhancement
-- Run this in phpMyAdmin against the 'seed cycle' database

USE `seed cycle`;

-- Ensure shipments table has all required columns
CREATE TABLE IF NOT EXISTS `shipments` (
  `id`                INT(11)      NOT NULL AUTO_INCREMENT,
  `order_id`          INT(11)      NOT NULL,
  `courier`           VARCHAR(100) NOT NULL DEFAULT '',
  `tracking_number`   VARCHAR(100) NOT NULL DEFAULT '',
  `status`            ENUM('pending','packed','shipped','in_transit','out_for_delivery','delivered') NOT NULL DEFAULT 'pending',
  `estimated_delivery` DATE         NULL,
  `notes`             TEXT         NULL,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Shipment status history log for timeline display
CREATE TABLE IF NOT EXISTS `shipment_logs` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `shipment_id` INT(11)      NOT NULL,
  `status`      ENUM('pending','packed','shipped','in_transit','out_for_delivery','delivered') NOT NULL,
  `note`        VARCHAR(255) NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shipment_id` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure orders table has all required shipping columns
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `shipping_status` ENUM('pending','packed','shipped','in_transit','out_for_delivery','delivered') NOT NULL DEFAULT 'pending' AFTER `status`,
  ADD COLUMN IF NOT EXISTS `delivery_method`  VARCHAR(50)  NULL AFTER `shipping_status`,
  ADD COLUMN IF NOT EXISTS `street_address`   VARCHAR(255) NULL AFTER `delivery_method`,
  ADD COLUMN IF NOT EXISTS `barangay`         VARCHAR(100) NULL AFTER `street_address`,
  ADD COLUMN IF NOT EXISTS `city`             VARCHAR(100) NULL AFTER `barangay`,
  ADD COLUMN IF NOT EXISTS `municipality`     VARCHAR(100) NULL AFTER `city`,
  ADD COLUMN IF NOT EXISTS `province`         VARCHAR(100) NULL AFTER `municipality`,
  ADD COLUMN IF NOT EXISTS `zip_code`         VARCHAR(20)  NULL AFTER `province`;
