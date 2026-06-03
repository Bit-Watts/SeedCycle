-- ============================================================================
-- Fix duplicate chat conversations
-- Run this once in phpMyAdmin to clean up duplicate order conversations
-- ============================================================================

USE `seed cycle`;

-- Step 1: Move messages from duplicate conversations to the original (oldest) one
UPDATE chat_messages cm
JOIN chat_conversations dup ON dup.id = cm.conversation_id
JOIN (
    SELECT order_id, MIN(id) AS keep_id
    FROM chat_conversations
    WHERE order_id IS NOT NULL AND conversation_type = 'order'
    GROUP BY order_id
    HAVING COUNT(*) > 1
) keeper ON keeper.order_id = dup.order_id
SET cm.conversation_id = keeper.keep_id
WHERE dup.id != keeper.keep_id;

-- Step 2: Delete the duplicate conversations (keep only the oldest per order)
DELETE dup
FROM chat_conversations dup
JOIN (
    SELECT order_id, MIN(id) AS keep_id
    FROM chat_conversations
    WHERE order_id IS NOT NULL AND conversation_type = 'order'
    GROUP BY order_id
) keeper ON keeper.order_id = dup.order_id
WHERE dup.id != keeper.keep_id
  AND dup.conversation_type = 'order';

-- Verify: should show no order_id with count > 1
SELECT order_id, COUNT(*) as cnt
FROM chat_conversations
WHERE order_id IS NOT NULL AND conversation_type = 'order'
GROUP BY order_id
HAVING cnt > 1;
