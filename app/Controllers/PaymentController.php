<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Cart.php';

class PaymentController {

    private $conn;
    private Order $orderModel;
    private Cart $cartModel;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->orderModel = new Order($conn);
        $this->cartModel = new Cart($conn);
    }

    private function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Generate a simulated GCash payment reference
     */
    public static function generatePaymentReference(string $method = 'gcash'): string {
        $prefix = strtoupper(substr($method, 0, 3));
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        return "{$prefix}-{$timestamp}-{$random}";
    }

    /**
     * Simulate GCash payment processing
     */
    public function processGCashPayment(): void {
        $this->requireAuth();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);

        if ($orderId <= 0 || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid order or amount']);
            exit;
        }

        // Verify order belongs to user
        $stmt = mysqli_prepare($this->conn, 
            'SELECT id, total_amount, payment_status FROM orders WHERE id = ? AND user_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }

        if ($order['payment_status'] === 'paid') {
            echo json_encode(['success' => false, 'message' => 'Order already paid']);
            exit;
        }

        // Simulate payment processing delay
        usleep(1500000); // 1.5 seconds

        // 95% success rate simulation
        $success = (rand(1, 100) <= 95);

        if ($success) {
            $reference = self::generatePaymentReference('gcash');
            
            // Update order payment status
            $stmt = mysqli_prepare($this->conn,
                'UPDATE orders SET payment_status = "paid", payment_reference = ?, updated_at = NOW() WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'si', $reference, $orderId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Log transaction
            $this->logTransaction($orderId, 'gcash', 'paid', $reference, $amount);

            echo json_encode([
                'success' => true,
                'message' => 'Payment successful',
                'reference' => $reference,
                'order_id' => $orderId
            ]);
        } else {
            // Update order payment status to failed
            $stmt = mysqli_prepare($this->conn,
                'UPDATE orders SET payment_status = "failed", updated_at = NOW() WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'i', $orderId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Log failed transaction
            $this->logTransaction($orderId, 'gcash', 'failed', null, $amount);

            echo json_encode([
                'success' => false,
                'message' => 'Payment failed. Please try again.',
                'order_id' => $orderId
            ]);
        }
    }

    /**
     * Log payment transaction
     */
    private function logTransaction(int $orderId, string $method, string $status, ?string $reference, float $amount): void {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO payment_transactions (order_id, payment_method, payment_status, payment_reference, amount) 
             VALUES (?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'isssd', $orderId, $method, $status, $reference, $amount);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    /**
     * Payment success page
     */
    public function success(): void {
        $this->requireAuth();
        
        $orderId = (int)($_GET['order_id'] ?? 0);
        $reference = $_GET['reference'] ?? '';

        if ($orderId <= 0) {
            header('Location: orders.php');
            exit;
        }

        // Verify order belongs to user
        $stmt = mysqli_prepare($this->conn,
            'SELECT o.*, u.first_name, u.last_name 
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.id = ? AND o.user_id = ?'
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

        require __DIR__ . '/../Views/payment-success.php';
    }

    /**
     * Payment failed page
     */
    public function failed(): void {
        $this->requireAuth();
        
        $orderId = (int)($_GET['order_id'] ?? 0);

        if ($orderId <= 0) {
            header('Location: orders.php');
            exit;
        }

        // Verify order belongs to user
        $stmt = mysqli_prepare($this->conn,
            'SELECT * FROM orders WHERE id = ? AND user_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$order) {
            header('Location: orders.php');
            exit;
        }

        require __DIR__ . '/../Views/payment-failed.php';
    }
}
