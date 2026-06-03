<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/Shipment.php';

class UserOrderController {

    private $conn;
    private User   $userModel;
    private Order  $orderModel;
    private Cart   $cartModel;
    private Shipment $shipmentModel;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->userModel  = new User($conn);
        $this->orderModel = new Order($conn);
        $this->cartModel  = new Cart($conn);
        $this->shipmentModel = new Shipment($conn);
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        
        // Redirect admin users to admin panel
        if (($_SESSION['role'] ?? 'user') === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
        }
    }

    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $message = null;
        $error   = null;

        // Handle shipment creation/update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create_shipment') {
                $courier           = trim($_POST['courier']            ?? '');
                $trackingNumber    = trim($_POST['tracking_number']    ?? '');
                $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');

                if (!$orderId || !$courier || !$trackingNumber) {
                    $error = 'Order ID, courier, and tracking number are required.';
                } else {
                    // Verify this order contains seller's seeds
                    $stmt = mysqli_prepare($this->conn,
                        'SELECT COUNT(*) AS cnt FROM order_items oi
                         JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
                         WHERE oi.order_id = ? AND sl.user_id = ? AND sl.status = "approved"'
                    );
                    mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
                    mysqli_stmt_execute($stmt);
                    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                    mysqli_stmt_close($stmt);

                    if ((int)$row['cnt'] > 0) {
                        $this->shipmentModel->create($orderId, $courier, $trackingNumber, $estimatedDelivery);

                        // Update order status to processing and shipping_status to shipped
                        $stmt2 = mysqli_prepare($this->conn,
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
                    $stmt = mysqli_prepare($this->conn,
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
                        $this->shipmentModel->update($shipmentId, $courier, $trackingNumber, $estimatedDelivery, $status);

                        // Update order shipping_status; if delivered, update order status too
                        if ($status === 'delivered') {
                            $stmt2 = mysqli_prepare($this->conn,
                                'UPDATE orders SET shipping_status = ?, status = "delivered" WHERE id = ?'
                            );
                        } else {
                            $stmt2 = mysqli_prepare($this->conn,
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

        // Handle remove cancelled order
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_order'])) {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $this->orderModel->deleteCancelled($orderId, $_SESSION['user_id']);
            header('Location: orders.php');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);
        $orders = $this->orderModel->getByUser($userId);
        
        // Get orders where user is a seller (to show shipment management)
        $sellerOrders = $this->orderModel->getSellerOrders($userId);
        
        // Group seller orders by order_id
        $groupedSellerOrders = [];
        foreach ($sellerOrders as $o) {
          $oid = $o['order_id'];
          if (!isset($groupedSellerOrders[$oid])) {
            $groupedSellerOrders[$oid] = $o;
            $groupedSellerOrders[$oid]['seeds'] = [];
          }
          $groupedSellerOrders[$oid]['seeds'][] = [
            'name' => $o['seed_name'],
            'quantity' => $o['quantity'],
            'price' => $o['price']
          ];
        }

        // Make shipmentModel available to the view
        $shipmentModel = $this->shipmentModel;

        require __DIR__ . '/../Views/orders.php';
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

        $userId = $_SESSION['user_id'];
        $user   = $this->userModel->findById($userId);

        // Get order and verify it belongs to this user
        $stmt = mysqli_prepare($conn,
            'SELECT o.*, u.first_name, u.last_name
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.id = ? AND o.user_id = ?
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $userId);
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

    public function cancel(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $orderId = (int)($_POST['order_id'] ?? 0);
        $userId  = $_SESSION['user_id'];

        if ($orderId > 0) {
            // Verify order belongs to user and is cancellable
            $stmt = mysqli_prepare($this->conn,
                'SELECT id, status FROM orders WHERE id = ? AND user_id = ? LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 'ii', $orderId, $userId);
            mysqli_stmt_execute($stmt);
            $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($order && in_array($order['status'], ['pending', 'processing', 'confirmed'])) {
                $stmt2 = mysqli_prepare($conn,
                    'UPDATE orders SET status = "cancelled" WHERE id = ?'
                );
                mysqli_stmt_bind_param($stmt2, 'i', $orderId);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
            }
        }

        header('Location: orders.php');
        exit;
    }
}
