<?php
/**
 * ChatMigration.php
 * Ensures the chat_conversations table has the conversation_type column.
 * Safe to call on every request — only runs ALTER TABLE once.
 */

namespace App\Helpers;

class ChatMigration
{
    /**
     * Run the migration if the column doesn't exist yet.
     */
    public static function ensureDirectChatSchema($conn): void
    {
        $check = mysqli_query($conn,
            "SELECT COUNT(*) AS cnt
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = 'chat_conversations'
               AND COLUMN_NAME  = 'conversation_type'"
        );

        if (!$check || (int)(mysqli_fetch_assoc($check)['cnt'] ?? 0) > 0) {
            return; // already migrated
        }

        // Make order_id nullable for direct chats
        mysqli_query($conn,
            "ALTER TABLE `chat_conversations`
             MODIFY COLUMN `order_id` INT(11) NULL DEFAULT NULL"
        );

        // Add conversation_type column
        mysqli_query($conn,
            "ALTER TABLE `chat_conversations`
             ADD COLUMN `conversation_type`
               ENUM('order','direct') NOT NULL DEFAULT 'order'"
        );

        // Back-fill existing rows as order conversations
        mysqli_query($conn,
            "UPDATE `chat_conversations`
             SET `conversation_type` = 'order'
             WHERE `order_id` IS NOT NULL"
        );

        // Unique constraint — prevents duplicate direct conversations
        @mysqli_query($conn,
            "ALTER TABLE `chat_conversations`
             ADD UNIQUE KEY `uq_direct_chat`
               (`buyer_id`, `seller_id`, `conversation_type`)"
        );
    }
}
