<?php

class Order {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByUser(int $userId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT o.id, o.total_amount, o.status, o.shipping_status, o.created_at,
                    GROUP_CONCAT(i.name ORDER BY i.name SEPARATOR ", ") AS seed_names
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             JOIN inventory i   ON i.id = oi.inventory_id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $orders;
    }

    public function getItemsByOrder(int $orderId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT i.name, oi.quantity, oi.price
             FROM order_items oi
             JOIN inventory i ON i.id = oi.inventory_id
             WHERE oi.order_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $items  = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $items;
    }

    /** Get orders containing seeds sold by a specific seller */
    public function getSellerOrders(int $sellerId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT DISTINCT
                    o.id AS order_id,
                    o.status,
                    o.shipping_status,
                    o.delivery_method,
                    o.street_address,
                    o.barangay,
                    o.city,
                    o.municipality,
                    o.province,
                    o.zip_code,
                    o.created_at,
                    u.first_name AS buyer_first_name,
                    u.last_name  AS buyer_last_name,
                    i.name       AS seed_name,
                    oi.quantity,
                    oi.price
             FROM order_items oi
             JOIN orders o    ON oi.order_id    = o.id
             JOIN inventory i ON oi.inventory_id = i.id
             JOIN seed_listings sl ON sl.inventory_id = i.id
             JOIN users u     ON u.id = o.user_id
             WHERE sl.user_id = ?
               AND sl.status  = "approved"
             ORDER BY o.created_at DESC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $sellerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $orders;
    }
    public function deleteCancelled(int $orderId, int $userId): bool {
        // Verify it belongs to user and is cancelled
        $stmt = mysqli_prepare($this->conn,
            'SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = "cancelled" LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) return false;

        // Delete order items first, then order
        $stmt2 = mysqli_prepare($this->conn, 'DELETE FROM order_items WHERE order_id = ?');
        mysqli_stmt_bind_param($stmt2, 'i', $orderId);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $stmt3 = mysqli_prepare($this->conn, 'DELETE FROM orders WHERE id = ? AND status = "cancelled"');
        mysqli_stmt_bind_param($stmt3, 'i', $orderId);
        $ok = mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
        return $ok;
    }

    /** Admin: delete any cancelled order */
    public function adminDeleteCancelled(int $orderId): bool {
        $stmt = mysqli_prepare($this->conn,
            'SELECT id FROM orders WHERE id = ? AND status = "cancelled" LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) return false;

        $stmt2 = mysqli_prepare($this->conn, 'DELETE FROM order_items WHERE order_id = ?');
        mysqli_stmt_bind_param($stmt2, 'i', $orderId);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        $stmt3 = mysqli_prepare($this->conn, 'DELETE FROM orders WHERE id = ? AND status = "cancelled"');
        mysqli_stmt_bind_param($stmt3, 'i', $orderId);
        $ok = mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
        return $ok;
    }
}
