-- ============================================================================
-- Direct User-to-User Chat Migration
-- Run ONCE in phpMyAdmin or MySQL CLI:
--   source /path/to/database-direct-chat.sql
-- ============================================================================

USE `seed cycle`;

-- 1. Make order_id nullable (direct chats have no order)
ALTER TABLE `chat_conversations`
  MODIFY COLUMN `order_id` INT(11) NULL DEFAULT NULL;

-- 2. Add conversation_type column (safe to run multiple times)
ALTER TABLE `chat_conversations`
  ADD COLUMN IF NOT EXISTS `conversation_type`
    ENUM('order', 'direct') NOT NULL DEFAULT 'order';

-- 3. Back-fill existing rows as 'order' type
UPDATE `chat_conversations`
  SET `conversation_type` = 'order'
  WHERE `conversation_type` IS NULL OR `order_id` IS NOT NULL;

-- 4. Unique constraint: only one direct conversation per user pair
--    (DROP first in case it already exists from a previous run)
ALTER TABLE `chat_conversations`
  DROP INDEX IF EXISTS `uq_direct_chat`;

ALTER TABLE `chat_conversations`
  ADD UNIQUE KEY `uq_direct_chat` (`buyer_id`, `seller_id`, `conversation_type`);

-- ============================================================================
-- END — import this file then refresh the app
-- ============================================================================
