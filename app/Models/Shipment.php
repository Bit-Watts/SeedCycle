<?php

class Shipment {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getByOrder(int $orderId): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT * FROM shipments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function create(int $orderId, string $courier, string $trackingNumber, string $estimatedDelivery = ''): bool {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO shipments (order_id, courier, tracking_number, estimated_delivery, status)
             VALUES (?, ?, ?, ?, "pending")'
        );
        mysqli_stmt_bind_param($stmt, 'isss', $orderId, $courier, $trackingNumber, $estimatedDelivery);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function update(int $id, string $courier, string $trackingNumber, string $estimatedDelivery, string $status): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE shipments SET courier=?, tracking_number=?, estimated_delivery=?, status=? WHERE id=?'
        );
        mysqli_stmt_bind_param($stmt, 'ssssi', $courier, $trackingNumber, $estimatedDelivery, $status, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function getAll(): array {
        $result = mysqli_query($this->conn,
            'SELECT s.*, o.user_id, u.first_name, u.last_name
             FROM shipments s
             JOIN orders o ON o.id = s.order_id
             JOIN users u ON u.id = o.user_id
             ORDER BY s.created_at DESC'
        );
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }
}
