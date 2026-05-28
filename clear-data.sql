-- ============================================================
-- SeedCycle - Clear all non-admin data
-- Uses DELETE instead of TRUNCATE to avoid FK constraint errors
-- in phpMyAdmin which resets FOREIGN_KEY_CHECKS between statements.
-- ============================================================

-- Chat / WebSocket (no FK dependencies on other cleared tables)
DELETE FROM chat_messages;
DELETE FROM chat_conversations;
DELETE FROM order_notifications;
DELETE FROM websocket_connections;

-- Shipment logs first (child of shipments)
DELETE FROM shipment_logs;

-- Shipments (child of orders)
DELETE FROM shipments;

-- Reviews (child of orders + inventory)
DELETE FROM reviews;

-- Cart (child of users + inventory)
DELETE FROM cart;

-- Order items (child of orders + inventory)
DELETE FROM order_items;

-- Orders (child of users)
DELETE FROM orders;

-- Seed images (child of inventory)
DELETE FROM seed_images;

-- Seed listings (child of inventory + users)
DELETE FROM seed_listings;

-- Inventory
DELETE FROM inventory;

-- Remove all non-admin users
DELETE FROM users WHERE role != 'admin';

-- Reset 2FA on admin accounts
UPDATE users SET totp_secret = NULL, totp_enabled = 0 WHERE role = 'admin';
