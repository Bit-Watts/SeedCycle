-- SeedCycle Database Schema
-- 1. Open phpMyAdmin
-- 2. Select the 'seed cycle' database
-- 3. Click the SQL tab and paste + run everything below the USE line

USE `seed cycle`;

-- Add new columns to seed_listings for user-submitted seed details
ALTER TABLE seed_listings
  ADD COLUMN IF NOT EXISTS seed_name          VARCHAR(150)   NULL AFTER inventory_id,
  ADD COLUMN IF NOT EXISTS category           VARCHAR(50)    NULL AFTER seed_name,
  ADD COLUMN IF NOT EXISTS price              DECIMAL(10,2)  NULL AFTER category,
  ADD COLUMN IF NOT EXISTS description        TEXT           NULL AFTER price,
  ADD COLUMN IF NOT EXISTS planting_start_month INT(11)      NULL AFTER description,
  ADD COLUMN IF NOT EXISTS planting_end_month   INT(11)      NULL AFTER planting_start_month,
  ADD COLUMN IF NOT EXISTS growing_days         INT(11)      NULL AFTER planting_end_month,
  ADD COLUMN IF NOT EXISTS image_path           VARCHAR(255)  NULL AFTER growing_days;
