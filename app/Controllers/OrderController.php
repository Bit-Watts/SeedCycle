<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Cart.php';

class OrderController {

    private User  $userModel;
    private Order $orderModel;
    private Cart  $cartModel;

    public function __construct() {
        global $conn;
        $this->userModel  = new User($conn);
        $this->orderModel = new Order($conn);
        $this->cartModel  = new Cart($conn);
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    public function sellerOrders(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        global $conn;
        require_once __DIR__ . '/../Models/Shipment.php';
        $shipmentModel = new Shipment($conn);

        $user    = $this->userModel->findById($_SESSION['user_id']);
        $message = null;
        $error   = null;

        // Handle shipment creation/update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create_shipment') {
                $orderId           = (int)($_POST['order_id']          ?? 0);
                $courier           = trim($_POST['courier']            ?? '');
                $trackingNumber    = trim($_POST['tracking_number']    ?? '');
                $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');

                if (!$orderId || !$courier || !$trackingNumber) {
                    $error = 'Order ID, courier, and tracking number are required.';
                } else {
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

                    if ((int)$row['cnt'] > 0) {
                        $shipmentModel->create($orderId, $courier, $trackingNumber, $estimatedDelivery);

                        // Update order status to processing and shipping_status to shipped
                        $stmt2 = mysqli_prepare($conn,
                            'UPDATE orders SET status = "processing", shipping_status = "shipped" WHERE id = ?'
                        );
                        mysqli_stmt_bind_param($stmt2, 'i', $orderId);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_close($stmt2);

                        $message = 'Shipment created successfully.';
                    } else {
                        $error = 'You do not have permission to ship this order.';
                    }
                }
            } elseif ($action === 'update_shipment') {
                $shipmentId        = (int)($_POST['shipment_id']       ?? 0);
                $courier           = trim($_POST['courier']            ?? '');
                $trackingNumber    = trim($_POST['tracking_number']    ?? '');
                $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');
                $status            = trim($_POST['status']             ?? 'shipped');

                if ($shipmentId > 0) {
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

                    if ($shipmentOrder) {
                        $shipmentModel->update($shipmentId, $courier, $trackingNumber, $estimatedDelivery, $status);

                        // Update order shipping_status; if delivered, update order status too
                        if ($status === 'delivered') {
                            $stmt2 = mysqli_prepare($conn,
                                'UPDATE orders SET shipping_status = ?, status = "delivered" WHERE id = ?'
                            );
                        } else {
                            $stmt2 = mysqli_prepare($conn,
                                'UPDATE orders SET shipping_status = ? WHERE id = ?'
                            );
                        }
                        mysqli_stmt_bind_param($stmt2, 'si', $status, $shipmentOrder['order_id']);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_close($stmt2);

                        $message = 'Shipment updated successfully.';
                    } else {
                        $error = 'You do not have permission to update this shipment.';
                    }
                }
            }
        }

        // Get seller orders with shipment info
        $orders = $this->orderModel->getSellerOrders($_SESSION['user_id']);
        
        // Attach shipment info to each order
        foreach ($orders as &$order) {
            $shipment = $shipmentModel->getByOrder($order['order_id']);
            $order['shipment'] = $shipment;
        }
        unset($order);

        require __DIR__ . '/../Views/seller-orders.php';
    }

    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        // Handle remove cancelled order
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

    public function checkout(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
            $municipality    = trim($_POST['municipality']    ?? '');
            $province        = trim($_POST['province']        ?? '');
            $zip_code        = trim($_POST['zip_code']        ?? '');
            $delivery_method = trim($_POST['delivery_method'] ?? '');

            // Validate required fields
            if (!$barangay || !$city || !$municipality || !$province || !$zip_code || !$delivery_method) {
                $error = 'Please fill in all required address fields.';
            } elseif (empty($cartItems)) {
                $error = 'Your cart is empty.';
            } else {
                // Check stock availability for all items
                $stockOk = true;
                foreach ($cartItems as $item) {
                    if ($item['quantity'] > $item['stock_quantity']) {
                        $error = "Insufficient stock for: " . htmlspecialchars($item['name']) .
                                 " (available: {$item['stock_quantity']})";
                        $stockOk = false;
                        break;
                    }
                }

                if ($stockOk) {
                    // Begin transaction
                    mysqli_begin_transaction($conn);
                    try {
                        // Insert order
                        $userId = $_SESSION['user_id'];
                        $status = 'pending';
                        $shippingStatus = 'pending';
                        $stmt = mysqli_prepare($conn,
                            'INSERT INTO orders (user_id, total_amount, status, shipping_status, delivery_method,
                             street_address, barangay, city, municipality, province, zip_code)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                        );
                        mysqli_stmt_bind_param($stmt, 'idsssssssss',
                            $userId, $total, $status, $shippingStatus, $delivery_method,
                            $street_address, $barangay, $city, $municipality, $province, $zip_code
                        );
                        mysqli_stmt_execute($stmt);
                        $orderId = mysqli_insert_id($conn);
                        mysqli_stmt_close($stmt);

                        // Insert order items and decrement stock
                        foreach ($cartItems as $item) {
                            $invId    = (int)$item['inventory_id'];
                            $qty      = (int)$item['quantity'];
                            $price    = (float)$item['price'];

                            // Insert order item
                            $stmt2 = mysqli_prepare($conn,
                                'INSERT INTO order_items (order_id, inventory_id, quantity, price)
                                 VALUES (?, ?, ?, ?)'
                            );
                            mysqli_stmt_bind_param($stmt2, 'iiid', $orderId, $invId, $qty, $price);
                            mysqli_stmt_execute($stmt2);
                            mysqli_stmt_close($stmt2);

                            // Decrement stock
                            $stmt3 = mysqli_prepare($conn,
                                'UPDATE inventory SET stock_quantity = stock_quantity - ? WHERE id = ?'
                            );
                            mysqli_stmt_bind_param($stmt3, 'ii', $qty, $invId);
                            mysqli_stmt_execute($stmt3);
                            mysqli_stmt_close($stmt3);
                        }

                        // Clear cart
                        $this->cartModel->clearByUser($userId);

                        mysqli_commit($conn);

                        header('Location: orders.php');
                        exit;

                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        $error = 'Order failed. Please try again.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/checkout.php';
    }

    public function tracking(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        global $conn;

        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($orderId <= 0) {
            header('Location: orders.php');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user_id']);

        // Get order and verify it belongs to this user
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

        // Get order items
        $orderItems = $this->orderModel->getItemsByOrder($orderId);

        // Get shipment info
        $stmt2 = mysqli_prepare($conn,
            'SELECT * FROM shipments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt2, 'i', $orderId);
        mysqli_stmt_execute($stmt2);
        $shipment = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
        mysqli_stmt_close($stmt2);

        require __DIR__ . '/../Views/order-tracking.php';
    }
}
