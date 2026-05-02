<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Models/Seed.php';
require_once __DIR__ . '/../Models/SeedListing.php';
require_once __DIR__ . '/../Models/Shipment.php';

class AdminController {

    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    private function requireAdmin(): void {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../index.php');
            exit;
        }
    }

    public function dashboard(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        // Stats
        $totalUsers = (int)(mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COUNT(*) AS cnt FROM users'))['cnt'] ?? 0);
        $totalOrders = (int)(mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COUNT(*) AS cnt FROM orders'))['cnt'] ?? 0);
        $totalSeeds = (int)(mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COUNT(*) AS cnt FROM inventory WHERE is_active = 1'))['cnt'] ?? 0);
        $pendingListings = (int)(mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COUNT(*) AS cnt FROM seed_listings WHERE status = "pending"'))['cnt'] ?? 0);

        // Recent 10 orders
        $result = mysqli_query($this->conn,
            'SELECT o.id, o.total_amount, o.status, o.created_at,
                    u.first_name, u.last_name, u.email
             FROM orders o
             JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC
             LIMIT 10'
        );
        $recentOrders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $recentOrders[] = $row;
        }

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function users(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $action = $_POST['action'] ?? '';

            if ($userId > 0 && in_array($action, ['activate', 'deactivate'])) {
                $isActive = $action === 'activate' ? 1 : 0;
                $stmt = mysqli_prepare($this->conn,
                    'UPDATE users SET is_active = ? WHERE id = ?'
                );
                mysqli_stmt_bind_param($stmt, 'ii', $isActive, $userId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $message = 'User ' . ($action === 'activate' ? 'activated' : 'deactivated') . ' successfully.';
            }
        }

        $result = mysqli_query($this->conn,
            'SELECT id, first_name, last_name, username, email, role, is_active, created_at
             FROM users
             ORDER BY created_at DESC'
        );
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        require __DIR__ . '/../Views/admin/users.php';
    }

    public function seeds(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        $message = null;
        $error   = null;
        $editSeed = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'edit') {
                $name        = trim($_POST['name']        ?? '');
                $category    = trim($_POST['category']    ?? '');
                $description = trim($_POST['description'] ?? '');
                $price       = (float)($_POST['price']    ?? 0);
                $stock       = (int)($_POST['stock_quantity'] ?? 0);
                $startMonth  = (int)($_POST['planting_start_month'] ?? 0) ?: null;
                $endMonth    = (int)($_POST['planting_end_month']   ?? 0) ?: null;
                $growingDays = (int)($_POST['growing_days'] ?? 0) ?: null;

                if (!$name || $price <= 0) {
                    $error = 'Name and price are required.';
                } else {
                    $seedId = (int)($_POST['seed_id'] ?? 0);
                    $stmt = mysqli_prepare($this->conn,
                        'UPDATE inventory SET name=?, category=?, description=?, price=?,
                         stock_quantity=?, planting_start_month=?, planting_end_month=?, growing_days=?
                         WHERE id=?'
                    );
                    mysqli_stmt_bind_param($stmt, 'sssdiiiii',
                        $name, $category, $description, $price, $stock,
                        $startMonth, $endMonth, $growingDays, $seedId
                    );
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Seed updated successfully.';
                }
            } elseif ($action === 'delete') {
                $seedId = (int)($_POST['seed_id'] ?? 0);
                if ($seedId > 0) {
                    $stmt = mysqli_prepare($this->conn,
                        'UPDATE inventory SET is_active = 0 WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($stmt, 'i', $seedId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Seed deactivated.';
                }
            } elseif ($action === 'restore') {
                $seedId = (int)($_POST['seed_id'] ?? 0);
                if ($seedId > 0) {
                    $stmt = mysqli_prepare($this->conn,
                        'UPDATE inventory SET is_active = 1 WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($stmt, 'i', $seedId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Seed restored.';
                }
            }
        }

        // Load seed for editing
        if (isset($_GET['edit'])) {
            $editId = (int)$_GET['edit'];
            $stmt = mysqli_prepare($this->conn,
                'SELECT * FROM inventory WHERE id = ? LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 'i', $editId);
            mysqli_stmt_execute($stmt);
            $editSeed = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
        }

        $result = mysqli_query($this->conn,
            'SELECT * FROM inventory ORDER BY is_active DESC, created_at DESC'
        );
        $seeds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $seeds[] = $row;
        }

        require __DIR__ . '/../Views/admin/seeds.php';
    }

    public function listings(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $listingId = (int)($_POST['listing_id'] ?? 0);
            $action    = $_POST['action'] ?? '';

            if ($listingId > 0 && in_array($action, ['approve', 'reject'])) {
                $status = $action === 'approve' ? 'approved' : 'rejected';
                require_once __DIR__ . '/../Models/SeedListing.php';
                $listingModel = new SeedListing($this->conn);
                $listingModel->updateStatus($listingId, $status);
                $message = 'Listing ' . $status . '.';
            }
        }

        $result = mysqli_query($this->conn,
            'SELECT sl.id, sl.status, sl.created_at,
                    i.id AS inventory_id, i.name AS seed_name, i.category, i.price,
                    i.description, i.planting_start_month, i.planting_end_month, i.growing_days,
                    (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url,
                    u.first_name, u.last_name, u.email
             FROM seed_listings sl
             JOIN inventory i ON i.id = sl.inventory_id
             JOIN users u ON u.id = sl.user_id
             ORDER BY sl.created_at DESC'
        );
        $listings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listings[] = $row;
        }

        require __DIR__ . '/../Views/admin/listings.php';
    }

    public function orders(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId        = (int)($_POST['order_id']        ?? 0);
            $newStatus      = trim($_POST['status']           ?? '');
            $newShipStatus  = trim($_POST['shipping_status']  ?? '');
            $validStatuses  = ['pending','processing','confirmed','shipped','out_for_delivery','delivered','cancelled'];
            $validShipStatuses = ['pending','shipped','in_transit','out_for_delivery','delivered'];

            // Handle remove cancelled order
            if (isset($_POST['remove_order']) && $orderId > 0) {
                require_once __DIR__ . '/../Models/Order.php';
                $orderModel = new Order($this->conn);
                $orderModel->adminDeleteCancelled($orderId);
                $message = 'Cancelled order removed.';
            } elseif ($orderId > 0) {
                if ($newStatus && in_array($newStatus, $validStatuses)) {
                    $stmt = mysqli_prepare($this->conn,
                        'UPDATE orders SET status = ? WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($stmt, 'si', $newStatus, $orderId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Order status updated.';
                }
                if ($newShipStatus && in_array($newShipStatus, $validShipStatuses)) {
                    $stmt = mysqli_prepare($this->conn,
                        'UPDATE orders SET shipping_status = ? WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($stmt, 'si', $newShipStatus, $orderId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $message = 'Order updated.';
                }
            }
        }

        $result = mysqli_query($this->conn,
            'SELECT o.id, o.total_amount, o.status, o.shipping_status, o.delivery_method,
                    o.created_at, u.first_name, u.last_name, u.email,
                    GROUP_CONCAT(i.name ORDER BY i.name SEPARATOR ", ") AS seed_names
             FROM orders o
             JOIN users u ON u.id = o.user_id
             JOIN order_items oi ON oi.order_id = o.id
             JOIN inventory i ON i.id = oi.inventory_id
             GROUP BY o.id
             ORDER BY o.created_at DESC'
        );
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }

        require __DIR__ . '/../Views/admin/orders.php';
    }

    public function shipments(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        $message = null;
        $error   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add') {
                $orderId           = (int)($_POST['order_id']          ?? 0);
                $courier           = trim($_POST['courier']            ?? '');
                $trackingNumber    = trim($_POST['tracking_number']    ?? '');
                $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');
                $status            = trim($_POST['status']             ?? 'pending');

                if (!$orderId || !$courier || !$trackingNumber) {
                    $error = 'Order ID, courier, and tracking number are required.';
                } else {
                    $stmt = mysqli_prepare($this->conn,
                        'INSERT INTO shipments (order_id, courier, tracking_number, estimated_delivery, status)
                         VALUES (?, ?, ?, ?, ?)'
                    );
                    mysqli_stmt_bind_param($stmt, 'issss',
                        $orderId, $courier, $trackingNumber, $estimatedDelivery, $status
                    );
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Update order shipping_status and set to processing
                    $stmt2 = mysqli_prepare($this->conn,
                        'UPDATE orders SET shipping_status = ?, status = "processing" WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($stmt2, 'si', $status, $orderId);
                    mysqli_stmt_execute($stmt2);
                    mysqli_stmt_close($stmt2);

                    $message = 'Shipment added successfully.';
                }
            } elseif ($action === 'update') {
                $shipmentId        = (int)($_POST['shipment_id']       ?? 0);
                $courier           = trim($_POST['courier']            ?? '');
                $trackingNumber    = trim($_POST['tracking_number']    ?? '');
                $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');
                $status            = trim($_POST['status']             ?? 'pending');

                if ($shipmentId > 0) {
                    // If delivered, set delivered_at
                    if ($status === 'delivered') {
                        $stmt = mysqli_prepare($this->conn,
                            'UPDATE shipments SET courier=?, tracking_number=?, estimated_delivery=?, status=?, delivered_at=NOW()
                             WHERE id=?'
                        );
                        mysqli_stmt_bind_param($stmt, 'ssssi',
                            $courier, $trackingNumber, $estimatedDelivery, $status, $shipmentId
                        );
                    } else {
                        $stmt = mysqli_prepare($this->conn,
                            'UPDATE shipments SET courier=?, tracking_number=?, estimated_delivery=?, status=?
                             WHERE id=?'
                        );
                        mysqli_stmt_bind_param($stmt, 'ssssi',
                            $courier, $trackingNumber, $estimatedDelivery, $status, $shipmentId
                        );
                    }
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Update order shipping_status; if delivered, update order status too
                    if ($status === 'delivered') {
                        $stmt2 = mysqli_prepare($this->conn,
                            'UPDATE orders o JOIN shipments s ON s.order_id = o.id
                             SET o.shipping_status = ?, o.status = "delivered"
                             WHERE s.id = ?'
                        );
                    } else {
                        $stmt2 = mysqli_prepare($this->conn,
                            'UPDATE orders o JOIN shipments s ON s.order_id = o.id
                             SET o.shipping_status = ?
                             WHERE s.id = ?'
                        );
                    }
                    mysqli_stmt_bind_param($stmt2, 'si', $status, $shipmentId);
                    mysqli_stmt_execute($stmt2);
                    mysqli_stmt_close($stmt2);

                    $message = 'Shipment updated.';
                }
            }
        }

        $result = mysqli_query($this->conn,
            'SELECT s.*, o.user_id, u.first_name, u.last_name
             FROM shipments s
             JOIN orders o ON o.id = s.order_id
             JOIN users u ON u.id = o.user_id
             ORDER BY s.created_at DESC'
        );
        $shipments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $shipments[] = $row;
        }

        // Orders without shipments (for add form)
        $ordersResult = mysqli_query($this->conn,
            'SELECT o.id, u.first_name, u.last_name
             FROM orders o
             JOIN users u ON u.id = o.user_id
             WHERE o.id NOT IN (SELECT DISTINCT order_id FROM shipments)
             ORDER BY o.created_at DESC'
        );
        $unshippedOrders = [];
        while ($row = mysqli_fetch_assoc($ordersResult)) {
            $unshippedOrders[] = $row;
        }

        require __DIR__ . '/../Views/admin/shipments.php';
    }

    public function reports(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAdmin();

        require_once __DIR__ . '/../Models/Report.php';
        require_once __DIR__ . '/../Models/Review.php';
        $reportModel = new Report($this->conn);
        $reviewModel = new Review($this->conn);

        $reportMessage = null;

        // Handle report actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportId     = (int)($_POST['report_id']     ?? 0);
            $reportAction = trim($_POST['report_action']  ?? '');

            if ($reportId > 0) {
                if (in_array($reportAction, ['reviewed', 'dismissed'])) {
                    $reportModel->updateStatus($reportId, $reportAction);
                    $reportMessage = 'Report marked as ' . $reportAction . '.';
                } elseif ($reportAction === 'delete_review') {
                    $reviewId = (int)($_POST['review_id'] ?? 0);
                    if ($reviewId > 0) {
                        $reviewModel->adminDelete($reviewId);
                        $reportModel->updateStatus($reportId, 'reviewed');
                        $reportMessage = 'Review deleted and report marked as reviewed.';
                    }
                }
            }
        }

        // User reports
        $userReports   = $reportModel->getAll();
        $pendingReports = $reportModel->getPendingCount();

        // Total revenue
        $revenueRow = mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE status != "cancelled"'
        ));
        $totalRevenue = (float)($revenueRow['total'] ?? 0);

        // Revenue this month
        $monthRevenueRow = mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders
             WHERE status != "cancelled" AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())'
        ));
        $monthRevenue = (float)($monthRevenueRow['total'] ?? 0);

        // Most sold seeds (top 10)
        $topSeedsResult = mysqli_query($this->conn,
            'SELECT i.name, i.category, SUM(oi.quantity) AS total_sold, SUM(oi.quantity * oi.price) AS revenue
             FROM order_items oi
             JOIN inventory i ON i.id = oi.inventory_id
             GROUP BY oi.inventory_id
             ORDER BY total_sold DESC
             LIMIT 10'
        );
        $topSeeds = [];
        while ($row = mysqli_fetch_assoc($topSeedsResult)) {
            $topSeeds[] = $row;
        }

        // Recent activity logs
        $activityResult = mysqli_query($this->conn,
            'SELECT al.action, al.created_at, u.first_name, u.last_name, u.email
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT 20'
        );
        $activityLogs = [];
        while ($row = mysqli_fetch_assoc($activityResult)) {
            $activityLogs[] = $row;
        }

        // Order counts by status
        $statusResult = mysqli_query($this->conn,
            'SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status'
        );
        $ordersByStatus = [];
        while ($row = mysqli_fetch_assoc($statusResult)) {
            $ordersByStatus[$row['status']] = (int)$row['cnt'];
        }

        require __DIR__ . '/../Views/admin/reports.php';
    }
}
