<?php

require_once __DIR__ . '/../../config/Database.php';

class HomeController {

    public function landing(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Redirect logged-in users to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        // Fetch up to 3 active, in-stock seeds from the marketplace
        global $conn;
        $landingSeeds = [];

        if ($conn) {
            $currentMonth = (int)date('n');
            $result = mysqli_query($conn,
                'SELECT i.id, i.name, i.category, i.price, i.planting_start_month, i.planting_end_month,
                        (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url
                 FROM inventory i
                 JOIN seed_listings sl ON sl.inventory_id = i.id
                 WHERE i.is_active = 1 AND i.stock_quantity > 0 AND sl.status = "approved"
                 GROUP BY i.id
                 ORDER BY i.created_at DESC
                 LIMIT 3'
            );
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $start = (int)($row['planting_start_month'] ?? 0);
                    $end   = (int)($row['planting_end_month']   ?? 0);
                    $row['in_season'] = $start && $end && $currentMonth >= $start && $currentMonth <= $end;
                    $landingSeeds[] = $row;
                }
            }
        }

        require __DIR__ . '/../Views/home.php';
    }
}
