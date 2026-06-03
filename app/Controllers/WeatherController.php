<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../config/Weather.php';
require_once __DIR__ . '/../Models/Weather.php';
require_once __DIR__ . '/../Models/Seed.php';

/**
 * WeatherController — handles the "Plant Today?" AJAX endpoint.
 * Returns JSON so the planting guide page can update without a full reload.
 */
class WeatherController {

    private function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
    }

    /**
     * GET/POST  planting-guide.php?weather=1&city=...
     *           planting-guide.php?weather=1&lat=...&lon=...
     *
     * Returns JSON.
     */
    public function check(): void {
        $this->startSession();
        header('Content-Type: application/json; charset=utf-8');

        global $conn;

        $apiKey = defined('OPENWEATHER_API_KEY') ? OPENWEATHER_API_KEY : '';
        if (!$apiKey || $apiKey === 'YOUR_OPENWEATHER_API_KEY_HERE') {
            echo json_encode(['error' => 'Weather API key is not configured. Open config/Weather.php and paste your OpenWeatherMap API key.']);
            exit;
        }

        $weatherModel = new Weather($apiKey);

        try {
            // Prefer coordinates (geolocation), fall back to city name
            $lat  = isset($_GET['lat'])  ? (float)$_GET['lat']  : null;
            $lon  = isset($_GET['lon'])  ? (float)$_GET['lon']  : null;
            $city = trim($_GET['city']  ?? '');

            if ($lat !== null && $lon !== null) {
                $weather = $weatherModel->fetchByCoords($lat, $lon);
            } elseif ($city !== '') {
                $weather = $weatherModel->fetchByCity($city);
            } else {
                echo json_encode(['error' => 'Please enter a city name or allow location access.']);
                exit;
            }

            // Analyze conditions
            $analysis = Weather::analyze($weather);

            // Get in-season seeds from inventory
            $currentMonth = (int)date('n');
            $result = mysqli_query($conn,
                'SELECT id, name, category, planting_start_month, planting_end_month, growing_days
                 FROM inventory
                 WHERE is_active = 1 AND planting_start_month IS NOT NULL
                 ORDER BY planting_start_month ASC, name ASC'
            );
            $allSeeds = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $allSeeds[] = $row;
            }

            $suggestedCrops = Weather::getSuggestedCrops($allSeeds, $weather, $currentMonth);

            // Log to DB (best-effort, don't fail if table missing)
            try {
                Weather::logCheck(
                    $conn,
                    $weather,
                    $analysis['status'],
                    $_SESSION['user_id'] ?? null
                );
            } catch (Throwable $e) {
                // Silently ignore logging errors
            }

            echo json_encode([
                'weather'        => $weather,
                'analysis'       => $analysis,
                'suggested_crops'=> $suggestedCrops,
                'current_month'  => $currentMonth,
            ]);

        } catch (RuntimeException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

        exit;
    }
}
