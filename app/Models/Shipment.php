<?php

class Shipment {

    private $conn;

    // Valid status progression order
    public const STATUSES = [
        'pending',
        'packed',
        'shipped',
        'in_transit',
        'out_for_delivery',
        'delivered',
    ];

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

    public function getById(int $id): ?array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT * FROM shipments WHERE id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    /**
     * Generate a unique internal tracking number in format SC-YYYYMMDD-XXXX.
     */
    public function generateTrackingNumber(): string {
        $date   = date('Ymd');
        $prefix = 'SC-' . $date . '-';

        // Find the highest sequence for today
        $stmt = mysqli_prepare($this->conn,
            "SELECT tracking_number FROM shipments
             WHERE tracking_number LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $like = $prefix . '%';
        mysqli_stmt_bind_param($stmt, 's', $like);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        $seq = 1;
        if ($row) {
            $parts = explode('-', $row['tracking_number']);
            $last  = end($parts);
            $seq   = ((int)$last) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new shipment record and log the initial status.
     */
    public function create(int $orderId, string $courier, string $trackingNumber,
                           string $estimatedDelivery = '', string $status = 'pending',
                           string $notes = ''): int|false {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO shipments (order_id, courier, tracking_number, estimated_delivery, status, notes)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $ed = $estimatedDelivery ?: null;
        mysqli_stmt_bind_param($stmt, 'isssss', $orderId, $courier, $trackingNumber, $ed, $status, $notes);
        $ok = mysqli_stmt_execute($stmt);
        $id = $ok ? (int)mysqli_insert_id($this->conn) : false;
        mysqli_stmt_close($stmt);

        if ($id) {
            $this->addLog($id, $status, 'Shipment created');
        }
        return $id;
    }

    /**
     * Update shipment details and log status change if status changed.
     */
    public function update(int $id, string $courier, string $trackingNumber,
                           string $estimatedDelivery, string $status, string $notes = ''): bool {
        // Get current status to detect change
        $current = $this->getById($id);
        $ed = $estimatedDelivery ?: null;

        // Set timestamps for specific statuses
        $shippedAt   = null;
        $deliveredAt = null;
        if ($status === 'shipped' || in_array($status, ['in_transit', 'out_for_delivery', 'delivered'])) {
            // Keep existing shipped_at or set now
            $shippedAt = $current['shipped_at'] ?? date('Y-m-d H:i:s');
        }
        if ($status === 'delivered') {
            $deliveredAt = date('Y-m-d H:i:s');
        }

        $stmt = mysqli_prepare($this->conn,
            'UPDATE shipments SET courier=?, tracking_number=?, estimated_delivery=?, status=?, notes=?,
             shipped_at=COALESCE(?, shipped_at), delivered_at=? WHERE id=?'
        );
        mysqli_stmt_bind_param($stmt, 'sssssssi',
            $courier, $trackingNumber, $ed, $status, $notes,
            $shippedAt, $deliveredAt, $id
        );
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Log status change
        if ($ok && $current && $current['status'] !== $status) {
            $label = ucwords(str_replace('_', ' ', $status));
            $this->addLog($id, $status, "Status updated to: {$label}");
        }
        return $ok;
    }

    /**
     * Add a log entry for a shipment status event.
     */
    public function addLog(int $shipmentId, string $status, string $note = ''): bool {
        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO shipment_logs (shipment_id, status, notes) VALUES (?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'iss', $shipmentId, $status, $note);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /**
     * Get all log entries for a shipment, ordered oldest first.
     */
    public function getLogs(int $shipmentId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT * FROM shipment_logs WHERE shipment_id = ? ORDER BY created_at ASC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $shipmentId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    /**
     * Get all shipments with buyer info (for admin monitoring).
     */
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

    /**
     * Get all shipments for orders belonging to a specific seller.
     */
    public function getBySellerOrders(int $sellerId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT DISTINCT s.*,
                    o.id AS order_id,
                    o.user_id AS buyer_id,
                    o.delivery_method,
                    o.street_address, o.barangay, o.city, o.municipality, o.province, o.zip_code,
                    o.created_at AS order_date,
                    u.first_name AS buyer_first_name,
                    u.last_name  AS buyer_last_name
             FROM shipments s
             JOIN orders o ON o.id = s.order_id
             JOIN order_items oi ON oi.order_id = o.id
             JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
             JOIN users u ON u.id = o.user_id
             WHERE sl.user_id = ? AND sl.status = "approved"
             ORDER BY s.created_at DESC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $sellerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    /**
     * Rule-based estimated delivery date calculation.
     * Compares seller province/city with buyer province/city.
     */
    public static function calcEstimatedDelivery(
        string $sellerProvince, string $sellerCity,
        string $buyerProvince,  string $buyerCity,
        string $deliveryMethod = 'deliver'
    ): string {
        if ($deliveryMethod === 'pickup') {
            // Pickup: same day or next day
            return date('Y-m-d', strtotime('+1 day'));
        }

        $sp = strtolower(trim($sellerProvince));
        $sc = strtolower(trim($sellerCity));
        $bp = strtolower(trim($buyerProvince));
        $bc = strtolower(trim($buyerCity));

        if ($sc === $bc && $sp === $bp) {
            // Same city → 1–2 days
            $days = 2;
        } elseif ($sp === $bp) {
            // Same province → 2–3 days
            $days = 3;
        } else {
            // Check nearby regions (simplified: NCR / Luzon / Visayas / Mindanao grouping)
            $luzon    = ['metro manila','ncr','bulacan','pampanga','bataan','nueva ecija','tarlac','zambales',
                         'laguna','batangas','cavite','rizal','quezon','aurora','bataan','benguet','ifugao',
                         'mountain province','kalinga','apayao','abra','ilocos norte','ilocos sur','la union',
                         'pangasinan','cagayan','isabela','nueva vizcaya','quirino','albay','camarines norte',
                         'camarines sur','catanduanes','masbate','sorsogon'];
            $visayas  = ['cebu','bohol','leyte','samar','eastern samar','northern samar','western samar',
                         'biliran','iloilo','capiz','aklan','antique','guimaras','negros occidental',
                         'negros oriental','siquijor'];
            $mindanao = ['davao del norte','davao del sur','davao oriental','davao occidental','davao de oro',
                         'south cotabato','north cotabato','sultan kudarat','sarangani','general santos',
                         'zamboanga del norte','zamboanga del sur','zamboanga sibugay','misamis occidental',
                         'misamis oriental','bukidnon','camiguin','lanao del norte','lanao del sur',
                         'maguindanao','basilan','sulu','tawi-tawi','agusan del norte','agusan del sur',
                         'surigao del norte','surigao del sur','dinagat islands'];

            $getRegion = function(string $prov) use ($luzon, $visayas, $mindanao): string {
                if (in_array($prov, $luzon))    return 'luzon';
                if (in_array($prov, $visayas))  return 'visayas';
                if (in_array($prov, $mindanao)) return 'mindanao';
                return 'unknown';
            };

            $sellerRegion = $getRegion($sp);
            $buyerRegion  = $getRegion($bp);

            if ($sellerRegion !== 'unknown' && $sellerRegion === $buyerRegion) {
                // Nearby region (same island group) → 3–5 days
                $days = 5;
            } else {
                // Far region (different island group) → 5–7 days
                $days = 7;
            }
        }

        return date('Y-m-d', strtotime("+{$days} days"));
    }
}
