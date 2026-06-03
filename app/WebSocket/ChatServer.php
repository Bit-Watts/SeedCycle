<?php
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;
    protected $chatService;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
        
        // Initialize chat service
        require_once __DIR__ . '/../../config/Database.php';
        global $conn;
        $this->chatService = new ChatService($conn);
        
        echo "WebSocket Server Started\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        
        if (!$data || !isset($data['type'])) {
            return;
        }

        echo "Message received: " . $data['type'] . "\n";

        switch ($data['type']) {
            case 'auth':
                $this->handleAuth($from, $data);
                break;
                
            case 'chat_message':
                $this->handleChatMessage($from, $data);
                break;
                
            case 'order_update':
                $this->handleOrderUpdate($from, $data);
                break;
                
            case 'typing':
                $this->handleTyping($from, $data);
                break;
                
            case 'mark_read':
                $this->handleMarkRead($from, $data);
                break;
                
            case 'ping':
                $from->send(json_encode(['type' => 'pong']));
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Remove user from connections
        foreach ($this->userConnections as $userId => $connection) {
            if ($connection === $conn) {
                unset($this->userConnections[$userId]);
                echo "User {$userId} disconnected\n";
                break;
            }
        }
        
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    // ========================================================================
    // HANDLER METHODS
    // ========================================================================

    private function handleAuth(ConnectionInterface $conn, $data) {
        if (!isset($data['user_id'])) {
            return;
        }

        $userId = (int)$data['user_id'];
        $this->userConnections[$userId] = $conn;
        
        echo "User {$userId} authenticated\n";
        
        // Send authentication success
        $conn->send(json_encode([
            'type' => 'auth_success',
            'user_id' => $userId,
            'timestamp' => time()
        ]));
        
        // Send unread messages count
        $unreadCount = $this->chatService->getUnreadCount($userId);
        $conn->send(json_encode([
            'type' => 'unread_count',
            'count' => $unreadCount
        ]));
    }

    private function handleChatMessage(ConnectionInterface $from, $data) {
        if (!isset($data['conversation_id'], $data['sender_id'], $data['message'])) {
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $senderId = (int)$data['sender_id'];
        $message = $data['message'];

        // Save message to database
        $messageId = $this->chatService->saveMessage(
            $conversationId,
            $senderId,
            $message,
            'text'
        );

        if (!$messageId) {
            return;
        }

        // Get conversation details
        $conversation = $this->chatService->getConversation($conversationId);
        if (!$conversation) {
            return;
        }

        // Get sender info
        $sender = $this->chatService->getUserInfo($senderId);

        // Prepare message data
        $messageData = [
            'type' => 'chat_message',
            'message_id' => $messageId,
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'sender_name' => $sender['first_name'] . ' ' . $sender['last_name'],
            'message' => $message,
            'timestamp' => time()
        ];

        // Send to buyer
        if (isset($this->userConnections[$conversation['buyer_id']])) {
            $this->userConnections[$conversation['buyer_id']]->send(json_encode($messageData));
        }

        // Send to seller
        if (isset($this->userConnections[$conversation['seller_id']])) {
            $this->userConnections[$conversation['seller_id']]->send(json_encode($messageData));
        }

        echo "Chat message sent from user {$senderId}\n";
    }

    private function handleOrderUpdate(ConnectionInterface $from, $data) {
        if (!isset($data['order_id'], $data['status'], $data['user_id'])) {
            return;
        }

        $orderId = (int)$data['order_id'];
        $status = $data['status'];
        $userId = (int)$data['user_id'];
        $message = $data['message'] ?? "Order status updated to: {$status}";

        // Get order details
        $order = $this->chatService->getOrder($orderId);
        if (!$order) {
            return;
        }

        // Create or get conversation
        $conversationId = $this->chatService->getOrCreateConversation(
            $orderId,
            $order['user_id'],
            $order['seller_id']
        );

        // Save system message
        $messageId = $this->chatService->saveMessage(
            $conversationId,
            $userId,
            $message,
            'order_update'
        );

        // Create notification
        $this->chatService->createNotification(
            $orderId,
            $order['user_id'], // Notify buyer
            'order_update',
            'Order Update',
            $message
        );

        // Prepare update data
        $updateData = [
            'type' => 'order_update',
            'order_id' => $orderId,
            'status' => $status,
            'message' => $message,
            'timestamp' => time()
        ];

        // Send to buyer
        if (isset($this->userConnections[$order['user_id']])) {
            $this->userConnections[$order['user_id']]->send(json_encode($updateData));
        }

        // Send to seller
        if (isset($this->userConnections[$order['seller_id']])) {
            $this->userConnections[$order['seller_id']]->send(json_encode($updateData));
        }

        echo "Order update sent for order {$orderId}\n";
    }

    private function handleTyping(ConnectionInterface $from, $data) {
        if (!isset($data['conversation_id'], $data['user_id'])) {
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $userId = (int)$data['user_id'];
        $isTyping = $data['is_typing'] ?? true;

        $conversation = $this->chatService->getConversation($conversationId);
        if (!$conversation) {
            return;
        }

        // Get user info
        $user = $this->chatService->getUserInfo($userId);

        $typingData = [
            'type' => 'typing',
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'user_name' => $user['first_name'],
            'is_typing' => $isTyping
        ];

        // Send to the other user
        $recipientId = ($userId === $conversation['buyer_id']) 
            ? $conversation['seller_id'] 
            : $conversation['buyer_id'];

        if (isset($this->userConnections[$recipientId])) {
            $this->userConnections[$recipientId]->send(json_encode($typingData));
        }
    }

    private function handleMarkRead(ConnectionInterface $from, $data) {
        if (!isset($data['conversation_id'], $data['user_id'])) {
            return;
        }

        $conversationId = (int)$data['conversation_id'];
        $userId = (int)$data['user_id'];

        // Mark messages as read
        $this->chatService->markMessagesAsRead($conversationId, $userId);

        // Send confirmation
        $from->send(json_encode([
            'type' => 'messages_read',
            'conversation_id' => $conversationId
        ]));
    }
}
