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
        try {
            // Get order details
            $order = $this->chatService->getOrder($orderId);
            if (!$order) {
                return false;
            }

            // Create or get conversation
            $conversationId = $this->chatService->getOrCreateConversation(
                $orderId,
                $order['user_id'],
                $order['seller_id']
            );

            // Generate message if not provided
            if (!$message) {
                $message = $this->generateOrderUpdateMessage($status);
            }

            // Save system message to database
            $this->chatService->saveMessage(
                $conversationId,
                $order['seller_id'], // Seller is sending the update
                $message,
                'order_update'
            );

            // Create notification for buyer
            $this->chatService->createNotification(
                $orderId,
                $order['user_id'],
                'order_update',
                'Order Update',
                $message
            );

            return true;
        } catch (\Exception $e) {
            error_log('WebSocket notification failed: ' . $e->getMessage());
            return false;
        }
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
