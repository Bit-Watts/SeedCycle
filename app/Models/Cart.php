<?php

class Cart {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /** Get all cart items for a user joined with inventory details */
    public function getByUser(int $userId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT c.id, c.inventory_id, c.quantity,
                    i.name, i.category, i.price, i.stock_quantity
             FROM cart c
             JOIN inventory i ON i.id = c.inventory_id
             WHERE c.user_id = ?
             ORDER BY c.id ASC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $items  = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $items;
    }

    /** Add a seed to cart or increment quantity if already present */
    public function addItem(int $userId, int $inventoryId, int $quantity = 1): bool {
        // Check if already in cart
        $stmt = mysqli_prepare($this->conn,
            'SELECT id, quantity FROM cart WHERE user_id = ? AND inventory_id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $inventoryId);
        mysqli_stmt_execute($stmt);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            $stmt   = mysqli_prepare($this->conn,
                'UPDATE cart SET quantity = ? WHERE id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'ii', $newQty, $existing['id']);
        } else {
            $stmt = mysqli_prepare($this->conn,
                'INSERT INTO cart (user_id, inventory_id, quantity) VALUES (?, ?, ?)'
            );
            mysqli_stmt_bind_param($stmt, 'iii', $userId, $inventoryId, $quantity);
        }

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Update quantity of a specific cart row */
    public function updateQuantity(int $cartId, int $userId, int $quantity): bool {
        if ($quantity < 1) {
            return $this->removeItem($cartId, $userId);
        }
        $stmt = mysqli_prepare($this->conn,
            'UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'iii', $quantity, $cartId, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Remove a specific cart row */
    public function removeItem(int $cartId, int $userId): bool {
        $stmt = mysqli_prepare($this->conn,
            'DELETE FROM cart WHERE id = ? AND user_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $cartId, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Clear all cart items for a user (used after checkout) */
    public function clearByUser(int $userId): bool {
        $stmt = mysqli_prepare($this->conn, 'DELETE FROM cart WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Count total items in cart for a user */
    public function countByUser(int $userId): int {
        $stmt = mysqli_prepare($this->conn,
            'SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return (int)($row['total'] ?? 0);
    }
}
