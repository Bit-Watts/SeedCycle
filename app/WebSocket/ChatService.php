<?php
namespace App\WebSocket;

class ChatService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Get or create a conversation for an order
     */
    public function getOrCreateConversation(int $orderId, int $buyerId, int $sellerId): int {
        // Check if conversation exists for this order
        $stmt = mysqli_prepare($this->conn,
            "SELECT id FROM chat_conversations
             WHERE order_id = ? AND conversation_type = 'order' LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($result) {
            return (int)$result['id'];
        }

        // Create new order conversation
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO chat_conversations
                 (order_id, buyer_id, seller_id, conversation_type)
             VALUES (?, ?, ?, 'order')"
        );
        mysqli_stmt_bind_param($stmt, 'iii', $orderId, $buyerId, $sellerId);
        mysqli_stmt_execute($stmt);
        $conversationId = (int)mysqli_insert_id($this->conn);
        mysqli_stmt_close($stmt);

        return $conversationId;
    }

    /**
     * Get conversation details with participant info
     */
    public function getConversation(int $conversationId): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT cc.*,
                    buyer.first_name  AS buyer_first_name,
                    buyer.last_name   AS buyer_last_name,
                    buyer.profile_image AS buyer_avatar,
                    seller.first_name AS seller_first_name,
                    seller.last_name  AS seller_last_name,
                    seller.profile_image AS seller_avatar
             FROM chat_conversations cc
             JOIN users buyer  ON buyer.id  = cc.buyer_id
             JOIN users seller ON seller.id = cc.seller_id
             WHERE cc.id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $conversationId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        return $result ?: null;
    }

    /**
     * Save a chat message
     */
    public function saveMessage(int $conversationId, int $senderId, string $message, string $type = 'text'): int|false {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO chat_messages (conversation_id, sender_id, message, message_type) 
             VALUES (?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'iiss', $conversationId, $senderId, $message, $type);
        $success = mysqli_stmt_execute($stmt);
        $messageId = $success ? mysqli_insert_id($this->conn) : false;
        mysqli_stmt_close($stmt);

        if ($success) {
            // Update conversation last_message_at
            $stmt = mysqli_prepare($this->conn,
                'UPDATE chat_conversations SET last_message_at = NOW() WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'i', $conversationId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        return $messageId;
    }

    /**
     * Get messages for a conversation.
     * @param int $since  Only return messages with id > $since (for polling). 0 = all.
     */
    public function getMessages(int $conversationId, int $limit = 100, int $since = 0): array {
        if ($since > 0) {
            $stmt = mysqli_prepare($this->conn,
                'SELECT cm.*, u.first_name, u.last_name, u.profile_image
                 FROM chat_messages cm
                 JOIN users u ON u.id = cm.sender_id
                 WHERE cm.conversation_id = ? AND cm.id > ?
                 ORDER BY cm.created_at ASC
                 LIMIT ?'
            );
            mysqli_stmt_bind_param($stmt, 'iii', $conversationId, $since, $limit);
        } else {
            $stmt = mysqli_prepare($this->conn,
                'SELECT cm.*, u.first_name, u.last_name, u.profile_image
                 FROM chat_messages cm
                 JOIN users u ON u.id = cm.sender_id
                 WHERE cm.conversation_id = ?
                 ORDER BY cm.created_at DESC
                 LIMIT ?'
            );
            mysqli_stmt_bind_param($stmt, 'ii', $conversationId, $limit);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        mysqli_stmt_close($stmt);

        // Reverse so oldest is first (only needed when we fetched DESC)
        return $since > 0 ? $messages : array_reverse($messages);
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead(int $conversationId, int $userId): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE chat_messages 
             SET is_read = 1 
             WHERE conversation_id = ? 
             AND sender_id != ? 
             AND is_read = 0'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $conversationId, $userId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $success;
    }

    /**
     * Get unread message count for a user
     */
    public function getUnreadCount(int $userId): int {
        $stmt = mysqli_prepare($this->conn,
            'SELECT COUNT(*) as count
             FROM chat_messages cm
             JOIN chat_conversations cc ON cc.id = cm.conversation_id
             WHERE (cc.buyer_id = ? OR cc.seller_id = ?)
             AND cm.sender_id != ?
             AND cm.is_read = 0'
        );
        mysqli_stmt_bind_param($stmt, 'iii', $userId, $userId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        return (int)($result['count'] ?? 0);
    }

    /**
     * Get user conversations (both order-based and direct)
     */
    public function getUserConversations(int $userId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT cc.*,
                    buyer.first_name  AS buyer_first_name,
                    buyer.last_name   AS buyer_last_name,
                    buyer.profile_image AS buyer_avatar,
                    seller.first_name AS seller_first_name,
                    seller.last_name  AS seller_last_name,
                    seller.profile_image AS seller_avatar,
                    (SELECT cm2.message
                     FROM chat_messages cm2
                     WHERE cm2.conversation_id = cc.id
                     ORDER BY cm2.created_at DESC LIMIT 1) AS last_message,
                    (SELECT COUNT(*)
                     FROM chat_messages cm3
                     WHERE cm3.conversation_id = cc.id
                       AND cm3.sender_id != ?
                       AND cm3.is_read = 0) AS unread_count
             FROM chat_conversations cc
             JOIN users buyer  ON buyer.id  = cc.buyer_id
             JOIN users seller ON seller.id = cc.seller_id
             WHERE cc.buyer_id = ? OR cc.seller_id = ?
             ORDER BY COALESCE(cc.last_message_at, cc.created_at) DESC'
        );
        mysqli_stmt_bind_param($stmt, 'iii', $userId, $userId, $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $conversations = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $conversations[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $conversations;
    }

    /**
     * Get user info
     */
    public function getUserInfo(int $userId): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT id, first_name, last_name, email FROM users WHERE id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        return $result ?: null;
    }

    /**
     * Get order details
     */
    public function getOrder(int $orderId): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT o.*, 
                    (SELECT sl.user_id FROM order_items oi
                     JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
                     WHERE oi.order_id = o.id
                     LIMIT 1) as seller_id
             FROM orders o
             WHERE o.id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        return $result ?: null;
    }

    /**
     * Create notification
     */
    public function createNotification(int $orderId, int $userId, string $type, string $title, string $message): int|false {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO order_notifications (order_id, user_id, notification_type, title, message) 
             VALUES (?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'iisss', $orderId, $userId, $type, $title, $message);
        $success = mysqli_stmt_execute($stmt);
        $notificationId = $success ? mysqli_insert_id($this->conn) : false;
        mysqli_stmt_close($stmt);

        return $notificationId;
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications(int $userId, int $limit = 20): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT * FROM order_notifications 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT ?'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $notifications = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $notifications;
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(int $notificationId): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE order_notifications SET is_read = 1 WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'i', $notificationId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $success;
    }
}
