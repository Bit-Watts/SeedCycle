<?php

class SeedListing {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Insert into inventory + seed_images + seed_listings in one transaction.
     * Returns the new inventory_id on success, or false on failure.
     */
    public function createWithInventory(
        int    $userId,
        string $name,
        string $category,
        float  $price,
        string $description,
        ?int   $startMonth,
        ?int   $endMonth,
        ?int   $growingDays,
        int    $stockQuantity = 1,
        string $imageUrl = ''
    ): int|false {
        // Insert into inventory (is_active = 0 until approved)
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO inventory (name, category, description, price, stock_quantity,
             planting_start_month, planting_end_month, growing_days, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)'
        );
        mysqli_stmt_bind_param($stmt, 'sssdiiii',
            $name, $category, $description, $price, $stockQuantity,
            $startMonth, $endMonth, $growingDays
        );
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return false;
        }
        $inventoryId = (int)mysqli_insert_id($this->conn);
        mysqli_stmt_close($stmt);

        // Insert image if provided
        if ($imageUrl) {
            $stmt2 = mysqli_prepare($this->conn,
                'INSERT INTO seed_images (inventory_id, image_url) VALUES (?, ?)'
            );
            mysqli_stmt_bind_param($stmt2, 'is', $inventoryId, $imageUrl);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        }

        // Insert seed_listing (pending)
        $stmt3 = mysqli_prepare($this->conn,
            'INSERT INTO seed_listings (user_id, inventory_id, status) VALUES (?, ?, "pending")'
        );
        mysqli_stmt_bind_param($stmt3, 'ii', $userId, $inventoryId);
        if (!mysqli_stmt_execute($stmt3)) {
            mysqli_stmt_close($stmt3);
            return false;
        }
        mysqli_stmt_close($stmt3);

        return $inventoryId;
    }

    public function alreadyPendingByName(int $userId, string $name): bool {
        $stmt = mysqli_prepare($this->conn,
            'SELECT sl.id FROM seed_listings sl
             JOIN inventory i ON i.id = sl.inventory_id
             WHERE sl.user_id = ? AND i.name = ? AND sl.status = "pending" LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'is', $userId, $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    public function getByUser(int $userId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT sl.id, sl.status, sl.created_at,
                    i.id AS inventory_id, i.name AS seed_name, i.category, i.price,
                    i.stock_quantity,
                    (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url
             FROM seed_listings sl
             JOIN inventory i ON i.id = sl.inventory_id
             WHERE sl.user_id = ?
             ORDER BY sl.created_at DESC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result   = mysqli_stmt_get_result($stmt);
        $listings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listings[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $listings;
    }

    public function addStock(int $inventoryId, int $userId, int $qty): bool {
        // Verify this inventory item belongs to the user via seed_listings
        $stmt = mysqli_prepare($this->conn,
            'SELECT sl.id FROM seed_listings sl
             WHERE sl.inventory_id = ? AND sl.user_id = ? AND sl.status = "approved" LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $inventoryId, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $owns = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$owns || $qty < 1) return false;

        $stmt2 = mysqli_prepare($this->conn,
            'UPDATE inventory SET stock_quantity = stock_quantity + ? WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt2, 'ii', $qty, $inventoryId);
        $ok = mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
        return $ok;
    }

    public function getAll(): array {
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
        return $listings;
    }

    /**
     * Approve: activate the inventory item so it shows on marketplace.
     * Reject: soft-delete the inventory item.
     */
    public function updateStatus(int $listingId, string $status): bool {
        // Get inventory_id first
        $stmt2 = mysqli_prepare($this->conn,
            'SELECT inventory_id FROM seed_listings WHERE id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt2, 'i', $listingId);
        mysqli_stmt_execute($stmt2);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
        mysqli_stmt_close($stmt2);

        if (!$row) return false;
        $inventoryId = (int)$row['inventory_id'];

        // Update listing status
        $stmt = mysqli_prepare($this->conn,
            'UPDATE seed_listings SET status = ? WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'si', $status, $listingId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Activate or deactivate inventory
        // On approve: set is_active=1 and ensure stock_quantity >= 1
        if ($status === 'approved') {
            $stmt3 = mysqli_prepare($this->conn,
                'UPDATE inventory SET is_active = 1,
                 stock_quantity = CASE WHEN stock_quantity < 1 THEN 1 ELSE stock_quantity END
                 WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt3, 'i', $inventoryId);
        } else {
            $stmt3 = mysqli_prepare($this->conn,
                'UPDATE inventory SET is_active = 0 WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt3, 'i', $inventoryId);
        }
        $ok = mysqli_stmt_execute($stmt3);
        mysqli_stmt_close($stmt3);
        return $ok;
    }
}
