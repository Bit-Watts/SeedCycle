<?php
namespace App\WebSocket;

// Require ChatService if not autoloaded
if (!class_exists('App\WebSocket\ChatService')) {
    require_once __DIR__ . '/ChatService.php';
}

/**
 * WebSocket Notifier
 * 
 * Helper class to send notifications to WebSocket server
 * This uses HTTP requests to trigger WebSocket messages
 */
class WebSocketNotifier {
    private $chatService;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->chatService = new ChatService($conn);
    }

    /**
     * Notify about order status update
     * 
     * @param int $orderId Order ID
     * @param string $status New status
     * @param string $message Custom message (optional)
     * @return bool Success
     */
    public function notifyOrderUpdate(int $orderId, string $status, string $message = null): bool {
        // Generate message if not provided
        if (!$message) {
            $message = $this->generateOrderUpdateMessage($status);
        }

        try {
            // Get order details
            $order = $this->chatService->getOrder($orderId);
            if (!$order) {
                return false;
            }

            // Always create the DB notification for the buyer — this powers the badge
            $this->chatService->createNotification(
                $orderId,
                $order['user_id'],
                'shipment_' . $status,
                $this->generateNotificationTitle($status),
                $message
            );

            // Attempt to also save a chat message (non-critical — skip if seller_id missing)
            if (!empty($order['seller_id'])) {
                try {
                    $conversationId = $this->chatService->getOrCreateConversation(
                        $orderId,
                        $order['user_id'],
                        $order['seller_id']
                    );

                    // Deduplicate: don't re-save if the last message in the conversation
                    // is already the same order_update content
                    $lastMsg = $this->getLastMessage($conversationId);
                    if (!$lastMsg ||
                        $lastMsg['message_type'] !== 'order_update' ||
                        $lastMsg['message'] !== $message) {
                        $this->chatService->saveMessage(
                            $conversationId,
                            $order['seller_id'],
                            $message,
                            'order_update'
                        );
                    }
                } catch (\Exception $e) {
                    error_log('Chat message save failed (non-critical): ' . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log('Notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the last message in a conversation for deduplication.
     */
    private function getLastMessage(int $conversationId): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT message, message_type FROM chat_messages
             WHERE conversation_id = ?
             ORDER BY id DESC LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $conversationId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /**
     * Generate a short notification title based on status
     */
    private function generateNotificationTitle(string $status): string {
        $titles = [
            'pending'          => 'Order Received',
            'processing'       => 'Order Processing',
            'packed'           => 'Order Packed',
            'shipped'          => 'Order Shipped',
            'in_transit'       => 'Package In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered'        => 'Package Delivered',
            'cancelled'        => 'Order Cancelled',
        ];
        return $titles[$status] ?? 'Order Update';
    }

    /**
     * Notify about shipment creation
     * 
     * @param int $orderId Order ID
     * @param string $courier Courier name
     * @param string $trackingNumber Tracking number
     * @return bool Success
     */
    public function notifyShipmentCreated(int $orderId, string $courier, string $trackingNumber): bool {
        $message = "Your order has been shipped via {$courier}. Tracking number: {$trackingNumber}";
        return $this->notifyOrderUpdate($orderId, 'shipped', $message);
    }

    /**
     * Notify about shipment status update
     * 
     * @param int $orderId Order ID
     * @param string $status New shipment status
     * @return bool Success
     */
    public function notifyShipmentUpdate(int $orderId, string $status): bool {
        $message = $this->generateShipmentUpdateMessage($status);
        return $this->notifyOrderUpdate($orderId, $status, $message);
    }

    /**
     * Notify the seller that the buyer confirmed delivery.
     */
    public function notifySellerDeliveryConfirmed($conn, int $orderId): void {
        try {
            $stmt = mysqli_prepare($conn,
                'SELECT sl.user_id AS seller_id
                 FROM order_items oi
                 JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
                 WHERE oi.order_id = ? AND sl.status = "approved"
                 LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 'i', $orderId);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($row && !empty($row['seller_id'])) {
                $this->chatService->createNotification(
                    $orderId,
                    (int)$row['seller_id'],
                    'delivery_confirmed',
                    'Delivery Confirmed',
                    "The buyer has confirmed receipt of Order #{$orderId}. The order is now complete."
                );
            }
        } catch (\Exception $e) {
            error_log('Seller delivery notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate order update message based on status
     */
    private function generateOrderUpdateMessage(string $status): string {
        $messages = [
            'pending' => 'Your order is being processed.',
            'processing' => 'Your order is being prepared for shipment.',
            'packed' => 'Your order has been packed and is ready to ship.',
            'shipped' => 'Your order has been shipped.',
            'in_transit' => 'Your order is on the way.',
            'out_for_delivery' => 'Your order is out for delivery.',
            'delivered' => 'Your order has been delivered. Thank you for your purchase!',
            'cancelled' => 'Your order has been cancelled.',
        ];

        return $messages[$status] ?? "Order status updated to: {$status}";
    }

    /**
     * Generate shipment update message based on status
     */
    private function generateShipmentUpdateMessage(string $status): string {
        $messages = [
            'pending' => 'Shipment is being prepared.',
            'packed' => 'Your order has been packed.',
            'shipped' => 'Your order has been shipped.',
            'in_transit' => 'Your package is in transit.',
            'out_for_delivery' => 'Your package is out for delivery today.',
            'delivered' => 'Your package has been delivered!',
        ];

        return $messages[$status] ?? "Shipment status updated to: {$status}";
    }
}
