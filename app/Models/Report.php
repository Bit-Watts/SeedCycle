<?php

class Report {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Your reports table: id, reporter_id, reported_user_id, inventory_id,
     *                     reason, description, status, created_at
     * We use inventory_id for seed reports, reported_user_id for review reports (reviewer's user_id)
     */
    public function create(int $reporterId, string $type, int $targetId, string $reason, string $details = ''): bool {
        // Map type+targetId to the actual columns
        $inventoryId    = $type === 'seed'   ? $targetId : null;
        $reportedUserId = $type === 'review' ? $targetId : null;

        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO reports (reporter_id, reported_user_id, inventory_id, reason, description, status)
             VALUES (?, ?, ?, ?, ?, "pending")'
        );
        mysqli_stmt_bind_param($stmt, 'iiiss',
            $reporterId, $reportedUserId, $inventoryId, $reason, $details
        );
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function alreadyReported(int $reporterId, string $type, int $targetId): bool {
        if ($type === 'seed') {
            $stmt = mysqli_prepare($this->conn,
                'SELECT id FROM reports WHERE reporter_id = ? AND inventory_id = ? LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 'ii', $reporterId, $targetId);
        } else {
            $stmt = mysqli_prepare($this->conn,
                'SELECT id FROM reports WHERE reporter_id = ? AND reported_user_id = ? LIMIT 1'
            );
            mysqli_stmt_bind_param($stmt, 'ii', $reporterId, $targetId);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    public function getAll(): array {
        $result = mysqli_query($this->conn,
            'SELECT r.id, r.reason, r.description, r.status, r.created_at,
                    r.inventory_id, r.reported_user_id,
                    u.first_name AS reporter_first, u.last_name AS reporter_last, u.email AS reporter_email,
                    i.name AS seed_name
             FROM reports r
             JOIN users u ON u.id = r.reporter_id
             LEFT JOIN inventory i ON i.id = r.inventory_id
             ORDER BY r.created_at DESC'
        );
        $reports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Determine type from which column is set
            $row['type']      = $row['inventory_id'] ? 'seed' : 'review';
            $row['target_id'] = $row['inventory_id'] ?? $row['reported_user_id'];
            $reports[] = $row;
        }
        return $reports;
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE reports SET status = ? WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function getPendingCount(): int {
        $row = mysqli_fetch_assoc(mysqli_query($this->conn,
            'SELECT COUNT(*) AS cnt FROM reports WHERE status = "pending"'
        ));
        return (int)($row['cnt'] ?? 0);
    }
}
