<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Shipment.php';
require_once __DIR__ . '/../WebSocket/ChatService.php';
require_once __DIR__ . '/../WebSocket/WebSocketNotifier.php';

class OrderController {

    private User     $userModel;
    private Order    $orderModel;
    private Cart     $cartModel;
    private Shipment $shipmentModel;
    private $wsNotifier; // Changed from typed property to avoid issues

    public function __construct() {
        global $conn;
        $this->userModel     = new User($conn);
        $this->orderModel    = new Order($conn);
        $this->cartModel     = new Cart($conn);
        $this->shipmentModel = new Shipment($conn);
        $this->wsNotifier    = new \App\WebSocket\WebSocketNotifier($conn);
    }

    private function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        if (($_SESSION['role'] ?? 'user') === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SELLER ORDERS PAGE
    // ─────────────────────────────────────────────────────────────────────────
    /**
     * Handle POST: confirm_receipt — buyer confirms delivery.
     */
    public function confirmReceipt(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        global $conn;

        $orderId = (int)($_POST['order_id'] ?? 0);
        if ($orderId <= 0) {
            header('Location: orders.php');
            exit;
        }

        // Verify this order belongs to the buyer and is out_for_delivery
        $stmt = mysqli_prepare($conn,
            'SELECT o.id, s.id AS shipment_id, s.courier, s.tracking_number, s.estimated_delivery, s.notes
             FROM orders o
             JOIN shipments s ON s.order_id = o.id
             WHERE o.id = ? AND o.user_id = ? AND s.status = "out_for_delivery"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($row) {
            $this->shipmentModel->update(
                (int)$row['shipment_id'],
                $row['courier'],
                $row['tracking_number'],
                $row['estimated_delivery'] ?? '',
                'delivered',
                $row['notes'] ?? ''
            );
            $this->syncOrderStatus($conn, $orderId, 'delivered');

            // Notify buyer (confirmation) and seller (buyer confirmed delivery)
            $this->wsNotifier->notifyShipmentUpdate($orderId, 'delivered');
            $this->wsNotifier->notifySellerDeliveryConfirmed($conn, $orderId);
        }

        header('Location: order-tracking.php?id=' . $orderId);
        exit;
    }

    public function sellerOrders(): void {
        $this->requireAuth();
        global $conn;

        $user    = $this->userModel->findById($_SESSION['user_id']);
        $message = null;
        $error   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create_shipment') {
                [$message, $error] = $this->handleCreateShipment($conn);
            } elseif ($action === 'update_shipment') {
                [$message, $error] = $this->handleUpdateShipment($conn);
            } elseif ($action === 'quick_status') {
                [$message, $error] = $this->handleQuickStatus($conn);
            }
        }

        // Get seller orders with shipment info attached
        $orders = $this->orderModel->getSellerOrders($_SESSION['user_id']);
        foreach ($orders as &$order) {
            $order['shipment'] = $this->shipmentModel->getByOrder($order['order_id']);
        }
        unset($order);

        // Get seller's province/city for estimated delivery calculation
        $sellerAddress = $this->getSellerAddress($conn, $_SESSION['user_id']);

        require __DIR__ . '/../Views/seller-orders.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ORDER TRACKING PAGE (Buyer)
    // ─────────────────────────────────────────────────────────────────────────
    public function tracking(): void {
        $this->requireAuth();
        global $conn;

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($orderId <= 0) {
            header('Location: orders.php');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);

        // Get order — must belong to this user
        $stmt = mysqli_prepare($conn,
            'SELECT o.*, u.first_name, u.last_name
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.id = ? AND o.user_id = ?
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$order) {
            header('Location: orders.php');
            exit;
        }

        $orderItems = $this->orderModel->getItemsByOrder($orderId);
        $shipment   = $this->shipmentModel->getByOrder($orderId);
        $shipmentLogs = $shipment ? $this->shipmentModel->getLogs((int)$shipment['id']) : [];

        require __DIR__ . '/../Views/order-tracking.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ORDERS LIST PAGE (Buyer)
    // ─────────────────────────────────────────────────────────────────────────
    public function index(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_order'])) {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $this->orderModel->deleteCancelled($orderId, $_SESSION['user_id']);
            header('Location: orders.php');
            exit;
        }

        $user   = $this->userModel->findById($_SESSION['user_id']);
        $orders = $this->orderModel->getByUser($_SESSION['user_id']);

        require __DIR__ . '/../Views/orders.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHECKOUT
    // ─────────────────────────────────────────────────────────────────────────
    public function checkout(): void {
        $this->requireAuth();
        global $conn;

        $user      = $this->userModel->findById($_SESSION['user_id']);
        $cartItems = $this->cartModel->getByUser($_SESSION['user_id']);

        $total = array_reduce($cartItems, fn($carry, $item) =>
            $carry + ($item['price'] * $item['quantity']), 0
        );

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $street_address  = trim($_POST['street_address']  ?? '');
            $barangay        = trim($_POST['barangay']        ?? '');
            $city            = trim($_POST['city']            ?? '');
            $province        = trim($_POST['province']        ?? '');
            $zip_code        = trim($_POST['zip_code']        ?? '');
            $delivery_method = trim($_POST['delivery_method'] ?? '');
            $payment_method  = trim($_POST['payment_method']  ?? 'cod');

            if (!$barangay || !$city || !$province || !$zip_code || !$delivery_method) {
                $error = 'Please fill in all required address fields.';
            } elseif (empty($cartItems)) {
                $error = 'Your cart is empty.';
            } elseif (!in_array($payment_method, ['cod', 'gcash'])) {
                $error = 'Invalid payment method selected.';
            } else {
                $stockOk = true;
                foreach ($cartItems as $item) {
                    if ($item['quantity'] > $item['stock_quantity']) {
                        $error   = "Insufficient stock for: " . htmlspecialchars($item['name']) .
                                   " (available: {$item['stock_quantity']})";
                        $stockOk = false;
                        break;
                    }
                }

                if ($stockOk) {
                    mysqli_begin_transaction($conn);
                    try {
                        $userId         = $_SESSION['user_id'];
                        $status         = 'pending';
                        $shippingStatus = 'pending';
                        
                        // Set payment status based on method
                        $paymentStatus = ($payment_method === 'cod') ? 'cod' : 'pending';
                        
                        $stmt = mysqli_prepare($conn,
                            'INSERT INTO orders (user_id, total_amount, status, shipping_status, delivery_method,
                             street_address, barangay, city, province, zip_code, payment_method, payment_status)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                        );
                        
                        if (!$stmt) {
                            throw new Exception('Prepare failed: ' . mysqli_error($conn));
                        }
                        
                        mysqli_stmt_bind_param($stmt, 'idssssssssss',
                            $userId, $total, $status, $shippingStatus, $delivery_method,
                            $street_address, $barangay, $city, $province, $zip_code,
                            $payment_method, $paymentStatus
                        );
                        
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception('Execute failed: ' . mysqli_stmt_error($stmt));
                        }
                        
                        $orderId = mysqli_insert_id($conn);
                        mysqli_stmt_close($stmt);

                        foreach ($cartItems as $item) {
                            $invId = (int)$item['inventory_id'];
                            $qty   = (int)$item['quantity'];
                            $price = (float)$item['price'];

                            $stmt2 = mysqli_prepare($conn,
                                'INSERT INTO order_items (order_id, inventory_id, quantity, price) VALUES (?, ?, ?, ?)'
                            );
                            
                            if (!$stmt2) {
                                throw new Exception('Prepare order_items failed: ' . mysqli_error($conn));
                            }
                            
                            mysqli_stmt_bind_param($stmt2, 'iiid', $orderId, $invId, $qty, $price);
                            
                            if (!mysqli_stmt_execute($stmt2)) {
                                throw new Exception('Execute order_items failed: ' . mysqli_stmt_error($stmt2));
                            }
                            
                            mysqli_stmt_close($stmt2);

                            $stmt3 = mysqli_prepare($conn,
                                'UPDATE inventory SET stock_quantity = stock_quantity - ? WHERE id = ?'
                            );
                            
                            if (!$stmt3) {
                                throw new Exception('Prepare inventory update failed: ' . mysqli_error($conn));
                            }
                            
                            mysqli_stmt_bind_param($stmt3, 'ii', $qty, $invId);
                            
                            if (!mysqli_stmt_execute($stmt3)) {
                                throw new Exception('Execute inventory update failed: ' . mysqli_stmt_error($stmt3));
                            }
                            
                            mysqli_stmt_close($stmt3);
                        }

                        mysqli_commit($conn);
                        
                        // Clear cart after successful order creation
                        $this->cartModel->clearByUser($userId);
                        
                        // Redirect based on payment method
                        if ($payment_method === 'cod') {
                            // COD: redirect to success page
                            header('Location: payment-success.php?order_id=' . $orderId . '&method=cod');
                            exit;
                        } else {
                            // GCash: redirect to payment page
                            header('Location: payment-gcash.php?order_id=' . $orderId . '&amount=' . $total);
                            exit;
                        }

                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        error_log('Order creation failed: ' . $e->getMessage());
                        error_log('MySQL error: ' . mysqli_error($conn));
                        $error = 'Order failed. Please try again. Error: ' . $e->getMessage();
                    }
                }
            }
        }

        require __DIR__ . '/../Views/checkout.php';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Handle POST: create_shipment
     * Returns [message, error]
     */
    private function handleCreateShipment($conn): array {
        $orderId           = (int)trim($_POST['order_id']          ?? 0);
        $courier           = trim($_POST['courier']                ?? '');
        $trackingNumber    = trim($_POST['tracking_number']        ?? '');
        $estimatedDelivery = trim($_POST['estimated_delivery']     ?? '');
        $notes             = trim($_POST['notes']                  ?? '');
        $initialStatus     = trim($_POST['initial_status']         ?? 'pending');

        if (!in_array($initialStatus, Shipment::STATUSES)) {
            $initialStatus = 'pending';
        }

        if (!$orderId || !$courier) {
            return [null, 'Order ID and courier are required.'];
        }

        // Auto-generate tracking number if empty
        if (!$trackingNumber) {
            $trackingNumber = $this->shipmentModel->generateTrackingNumber();
        }

        // Verify this order contains seller's seeds
        $stmt = mysqli_prepare($conn,
            'SELECT COUNT(*) AS cnt FROM order_items oi
             JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
             WHERE oi.order_id = ? AND sl.user_id = ? AND sl.status = "approved"'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ((int)$row['cnt'] === 0) {
            return [null, 'You do not have permission to ship this order.'];
        }

        // Check no shipment exists yet
        $existing = $this->shipmentModel->getByOrder($orderId);
        if ($existing) {
            return [null, 'A shipment already exists for this order. Use "Update Shipment" instead.'];
        }

        $shipmentId = $this->shipmentModel->create(
            $orderId, $courier, $trackingNumber, $estimatedDelivery, $initialStatus, $notes
        );

        if ($shipmentId) {
            // Sync order shipping_status
            $this->syncOrderStatus($conn, $orderId, $initialStatus);
            
            // Send WebSocket notification to buyer
            $this->wsNotifier->notifyShipmentCreated($orderId, $courier, $trackingNumber);
            
            return ['Shipment created successfully.', null];
        }
        return [null, 'Failed to create shipment. Please try again.'];
    }

    /**
     * Handle POST: update_shipment
     * Returns [message, error]
     */
    private function handleUpdateShipment($conn): array {
        $shipmentId        = (int)trim($_POST['shipment_id']       ?? 0);
        $courier           = trim($_POST['courier']                ?? '');
        $trackingNumber    = trim($_POST['tracking_number']        ?? '');
        $estimatedDelivery = trim($_POST['estimated_delivery']     ?? '');
        $status            = trim($_POST['status']                 ?? 'pending');
        $notes             = trim($_POST['notes']                  ?? '');

        if (!in_array($status, Shipment::STATUSES) || $status === 'delivered') {
            $status = 'out_for_delivery'; // cap at out_for_delivery for sellers
        }

        // Verify this shipment belongs to seller's order
        $stmt = mysqli_prepare($conn,
            'SELECT s.order_id FROM shipments s
             JOIN order_items oi ON oi.order_id = s.order_id
             JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
             WHERE s.id = ? AND sl.user_id = ? AND sl.status = "approved"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $shipmentId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $shipmentOrder = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$shipmentOrder) {
            return [null, 'You do not have permission to update this shipment.'];
        }

        $ok = $this->shipmentModel->update(
            $shipmentId, $courier, $trackingNumber, $estimatedDelivery, $status, $notes
        );

        if ($ok) {
            $this->syncOrderStatus($conn, (int)$shipmentOrder['order_id'], $status);
            
            // Send WebSocket notification to buyer
            $this->wsNotifier->notifyShipmentUpdate((int)$shipmentOrder['order_id'], $status);
            
            return ['Shipment updated successfully.', null];
        }
        return [null, 'Failed to update shipment.'];
    }

    /**
     * Sync orders.shipping_status (and status if delivered) with shipment status.
     * For COD orders, also mark payment as paid when delivered.
     */
    private function syncOrderStatus($conn, int $orderId, string $shipmentStatus): void {
        if ($shipmentStatus === 'delivered') {
            // Mark delivered; also set COD payment to paid
            $stmt = mysqli_prepare($conn,
                'UPDATE orders
                 SET shipping_status = ?,
                     status = "delivered",
                     payment_status = CASE WHEN payment_method = "cod" THEN "paid" ELSE payment_status END
                 WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'si', $shipmentStatus, $orderId);
        } else {
            $orderStatus = in_array($shipmentStatus, ['pending', 'packed']) ? 'pending' : 'processing';
            $stmt = mysqli_prepare($conn,
                'UPDATE orders SET shipping_status = ?, status = ? WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'ssi', $shipmentStatus, $orderStatus, $orderId);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /**
     * Handle POST: quick_status — one-click status update for a shipment.
     */
    private function handleQuickStatus($conn): array {
        $shipmentId = (int)trim($_POST['shipment_id'] ?? 0);
        $status     = trim($_POST['status']           ?? '');

        if (!in_array($status, Shipment::STATUSES) || $status === 'delivered') {
            return [null, 'Invalid status. Delivered status can only be confirmed by the buyer.'];
        }
        if ($shipmentId <= 0) {
            return [null, 'Invalid shipment.'];
        }

        // Verify seller owns the order
        $stmt = mysqli_prepare($conn,
            'SELECT s.order_id, s.courier, s.tracking_number, s.estimated_delivery, s.notes
             FROM shipments s
             JOIN order_items oi ON oi.order_id = s.order_id
             JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
             WHERE s.id = ? AND sl.user_id = ? AND sl.status = "approved"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $shipmentId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$row) {
            return [null, 'Permission denied.'];
        }

        $ok = $this->shipmentModel->update(
            $shipmentId,
            $row['courier'],
            $row['tracking_number'],
            $row['estimated_delivery'] ?? '',
            $status,
            $row['notes'] ?? ''
        );

        if ($ok) {
            $this->syncOrderStatus($conn, (int)$row['order_id'], $status);
            $this->wsNotifier->notifyShipmentUpdate((int)$row['order_id'], $status);
            $label = ucwords(str_replace('_', ' ', $status));
            return ["Order marked as: {$label}", null];
        }
        return [null, 'Failed to update status.'];
    }

    /**
     * Get seller's province and city from their most recent order or profile.
     */
    private function getSellerAddress($conn, int $sellerId): array {
        // Try to get from a recent order they placed as buyer
        $stmt = mysqli_prepare($conn,
            'SELECT province, city FROM orders WHERE user_id = ? AND province != "" ORDER BY created_at DESC LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $sellerId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return [
            'province' => $row['province'] ?? '',
            'city'     => $row['city']     ?? '',
        ];
    }
}
