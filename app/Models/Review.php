<?php

class Review {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByInventory(int $inventoryId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT r.id, r.rating, r.comment, r.created_at,
                    u.first_name, u.last_name, u.username, u.profile_image
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.inventory_id = ?
             ORDER BY r.created_at DESC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $inventoryId);
        mysqli_stmt_execute($stmt);
        $result  = mysqli_stmt_get_result($stmt);
        $reviews = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $reviews[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $reviews;
    }

    public function getAverageRating(int $inventoryId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total
             FROM reviews WHERE inventory_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'i', $inventoryId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return [
            'avg'   => (float)($row['avg_rating'] ?? 0),
            'total' => (int)($row['total'] ?? 0),
        ];
    }

    public function hasReviewed(int $userId, int $inventoryId): bool {
        $stmt = mysqli_prepare($this->conn,
            'SELECT id FROM reviews WHERE user_id = ? AND inventory_id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $inventoryId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    /** Check if user has purchased this seed (required to review) */
    public function hasPurchased(int $userId, int $inventoryId): bool {
        $stmt = mysqli_prepare($this->conn,
            'SELECT oi.id FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             WHERE o.user_id = ? AND oi.inventory_id = ?
               AND o.status = "delivered"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $inventoryId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    public function getOrderIdForReview(int $userId, int $inventoryId): int {
        $stmt = mysqli_prepare($this->conn,
            'SELECT o.id FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             WHERE o.user_id = ? AND oi.inventory_id = ?
               AND o.status = "delivered"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $inventoryId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return (int)($row['id'] ?? 0);
    }

    public function create(int $userId, int $inventoryId, int $orderId, int $rating, string $comment): bool {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO reviews (user_id, inventory_id, order_id, rating, comment)
             VALUES (?, ?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'iiiis', $userId, $inventoryId, $orderId, $rating, $comment);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function delete(int $reviewId, int $userId): bool {
        $stmt = mysqli_prepare($this->conn,
            'DELETE FROM reviews WHERE id = ? AND user_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $reviewId, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function adminDelete(int $reviewId): bool {
        $stmt = mysqli_prepare($this->conn, 'DELETE FROM reviews WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $reviewId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}
