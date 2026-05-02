<?php

class Seed {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Convert month number to name
    public static function monthName(?int $m): string {
        if (!$m) return '';
        $names = ['','January','February','March','April','May','June',
                  'July','August','September','October','November','December'];
        return $names[$m] ?? '';
    }

    public function getAll(): array {
        $result = mysqli_query($this->conn,
            'SELECT i.id, i.name, i.category, i.price, i.stock_quantity,
                    i.planting_start_month, i.planting_end_month, i.growing_days,
                    (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url,
                    IFNULL(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
                    COUNT(r.id) AS review_count
             FROM inventory i
             LEFT JOIN reviews r ON r.inventory_id = i.id
             WHERE i.is_active = 1 AND i.stock_quantity > 0
             GROUP BY i.id
             ORDER BY i.created_at DESC'
        );
        $seeds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $seeds[] = $row;
        }
        return $seeds;
    }

    public function findById(int $id): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT i.id, i.name, i.category, i.description, i.price, i.stock_quantity,
                    i.planting_start_month, i.planting_end_month, i.growing_days,
                    (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url
             FROM inventory i
             WHERE i.id = ? AND i.is_active = 1 LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function findInStock(int $id): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT id, name, category, price
             FROM inventory
             WHERE id = ? AND is_active = 1 AND stock_quantity > 0 LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /** Check if a seed listing belongs to the given user */
    public function isOwnedBy(int $inventoryId, int $userId): bool {
        $stmt = mysqli_prepare($this->conn,
            'SELECT id FROM seed_listings WHERE inventory_id = ? AND user_id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $inventoryId, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $owned = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $owned;
    }
}
