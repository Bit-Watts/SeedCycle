<?php

/**
 * Weather — handles OpenWeatherMap API calls and planting recommendation logic.
 * All analysis is rule-based; no external AI or ML.
 */
class Weather {

    // ── OpenWeatherMap API ──────────────────────────────────────────────────
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    // ── FETCH CURRENT WEATHER ───────────────────────────────────────────────

    /**
     * Fetch current weather + 3-hour forecast (for rain probability) by city name.
     * Returns a normalized array or throws RuntimeException on failure.
     */
    public function fetchByCity(string $city): array {
        $url = "{$this->baseUrl}/weather?q=" . urlencode($city)
             . "&appid={$this->apiKey}&units=metric";

        $raw = $this->httpGet($url);
        if (!$raw || !isset($raw['main'])) {
            $owmMsg = $raw['message'] ?? '';
            if ($owmMsg) {
                throw new RuntimeException("OpenWeatherMap: {$owmMsg}");
            }
            throw new RuntimeException("City \"{$city}\" not found. Try a different spelling or add the country code (e.g. \"Manila,PH\").");
        }

        $forecastUrl = "{$this->baseUrl}/forecast?q=" . urlencode($city)
                     . "&appid={$this->apiKey}&units=metric&cnt=2";
        $forecast   = $this->httpGet($forecastUrl);
        $rainChance = $this->extractRainChance($forecast);

        return $this->normalize($raw, $rainChance);
    }

    /**
     * Fetch current weather by latitude/longitude (for geolocation).
     */
    public function fetchByCoords(float $lat, float $lon): array {
        $url = "{$this->baseUrl}/weather?lat={$lat}&lon={$lon}"
             . "&appid={$this->apiKey}&units=metric";

        $raw = $this->httpGet($url);
        if (!$raw || !isset($raw['main'])) {
            $owmMsg = $raw['message'] ?? '';
            if ($owmMsg) {
                throw new RuntimeException("OpenWeatherMap: {$owmMsg}");
            }
            throw new RuntimeException("Could not fetch weather for your coordinates. Check your API key.");
        }

        $forecastUrl = "{$this->baseUrl}/forecast?lat={$lat}&lon={$lon}"
                     . "&appid={$this->apiKey}&units=metric&cnt=2";
        $forecast   = $this->httpGet($forecastUrl);
        $rainChance = $this->extractRainChance($forecast);

        return $this->normalize($raw, $rainChance);
    }

    // ── PLANTING RECOMMENDATION ENGINE ─────────────────────────────────────

    /**
     * Analyze weather data and return a recommendation result.
     *
     * Returns:
     *   status      => 'good' | 'caution' | 'bad'
     *   title       => short headline
     *   message     => detailed explanation
     *   reasons     => array of reason strings
     */
    public static function analyze(array $weather): array {
        $temp       = (float)$weather['temperature'];
        $rain       = (int)$weather['rain_chance'];
        $humidity   = (int)$weather['humidity'];
        $condition  = strtolower($weather['condition_main'] ?? '');
        $conditionId = (int)($weather['condition_id'] ?? 800);

        $reasons = [];
        $badFlags = 0;
        $cautionFlags = 0;

        // ── RULE 1: Extreme weather (storms, thunderstorms, tornado) ──
        // OWM condition IDs: 2xx = Thunderstorm, 900-902 = extreme
        if ($conditionId >= 200 && $conditionId < 300) {
            $badFlags++;
            $reasons[] = 'Thunderstorm detected — unsafe for outdoor planting.';
        }
        if (in_array($condition, ['tornado', 'squall', 'ash', 'volcanic ash'])) {
            $badFlags++;
            $reasons[] = 'Extreme weather condition — avoid outdoor activity.';
        }

        // ── RULE 2: Heavy rain chance ──
        if ($rain > 70) {
            $badFlags++;
            $reasons[] = "High rain probability ({$rain}%) — seeds may wash away.";
        } elseif ($rain > 40) {
            $cautionFlags++;
            $reasons[] = "Moderate rain chance ({$rain}%) — consider planting in sheltered areas.";
        }

        // ── RULE 3: Temperature ──
        if ($temp < 15) {
            $cautionFlags++;
            $reasons[] = "Temperature is low ({$temp}°C) — most tropical seeds prefer 20–30°C.";
        } elseif ($temp > 38) {
            $badFlags++;
            $reasons[] = "Temperature is very high ({$temp}°C) — heat stress may damage seedlings.";
        } elseif ($temp > 33) {
            $cautionFlags++;
            $reasons[] = "Temperature is warm ({$temp}°C) — water seedlings immediately after planting.";
        } elseif ($temp >= 20 && $temp <= 30) {
            $reasons[] = "Temperature is ideal ({$temp}°C) for most seeds.";
        }

        // ── RULE 4: Humidity ──
        if ($humidity > 90) {
            $cautionFlags++;
            $reasons[] = "Very high humidity ({$humidity}%) — watch for fungal disease.";
        } elseif ($humidity < 30) {
            $cautionFlags++;
            $reasons[] = "Low humidity ({$humidity}%) — soil may dry out quickly; water more frequently.";
        }

        // ── RULE 5: Clear/Cloudy is good ──
        if (in_array($condition, ['clear', 'clouds']) && $rain <= 40 && $temp >= 20 && $temp <= 33) {
            $reasons[] = ucfirst($condition) . ' skies — good light conditions for planting.';
        }

        // ── DETERMINE OVERALL STATUS ──
        if ($badFlags > 0) {
            $status  = 'bad';
            $title   = 'Not Recommended to Plant Today';
            $message = 'Current weather conditions are unfavorable for planting. '
                     . 'Wait for better conditions to protect your seeds.';
        } elseif ($cautionFlags > 0) {
            $status  = 'caution';
            $title   = 'Plant with Caution';
            $message = 'Conditions are acceptable but not ideal. '
                     . 'Take extra care when planting today.';
        } else {
            $status  = 'good';
            $title   = 'Good Day to Plant!';
            $message = 'Weather conditions are favorable. '
                     . 'Today is a great time to get your seeds in the ground.';
        }

        return compact('status', 'title', 'message', 'reasons');
    }

    /**
     * Get seeds from inventory that are in-season for the current month
     * AND suitable for the given weather conditions.
     *
     * @param array $allSeeds  — from inventory (planting_start_month, planting_end_month, name, category)
     * @param array $weather   — normalized weather array
     * @param int   $month     — current month (1–12)
     */
    public static function getSuggestedCrops(array $allSeeds, array $weather, int $month): array {
        $temp      = (float)$weather['temperature'];
        $rain      = (int)$weather['rain_chance'];
        $condition = strtolower($weather['condition_main'] ?? '');

        $suggested = [];
        foreach ($allSeeds as $seed) {
            $start = (int)($seed['planting_start_month'] ?? 0);
            $end   = (int)($seed['planting_end_month']   ?? $start);
            if ($start < 1) continue;

            // In season this month?
            if ($month < $start || $month > $end) continue;

            // Skip if weather is bad
            if ($rain > 70) continue;

            $suggested[] = $seed;
        }

        // Limit to 8 suggestions
        return array_slice($suggested, 0, 8);
    }

    // ── DATABASE LOGGING ───────────────────────────────────────────────────

    public static function logCheck($conn, array $weather, string $recommendation, ?int $userId = null): void {
        $stmt = mysqli_prepare($conn,
            'INSERT INTO weather_logs
             (user_id, city, country, temperature, feels_like, humidity, rain_chance,
              weather_condition, weather_icon, wind_speed, recommendation)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        // Types: i=int, s=string, d=double
        // user_id(i), city(s), country(s), temperature(d), feels_like(d),
        // humidity(i), rain_chance(i), weather_condition(s), weather_icon(s),
        // wind_speed(d), recommendation(s)
        $userId_val   = $userId;
        $city         = $weather['city']          ?? '';
        $country      = $weather['country']        ?? '';
        $temperature  = (float)($weather['temperature'] ?? 0);
        $feels_like   = (float)($weather['feels_like']  ?? 0);
        $humidity     = (int)($weather['humidity']      ?? 0);
        $rain_chance  = (int)($weather['rain_chance']   ?? 0);
        $condition    = $weather['condition_desc']  ?? '';
        $icon         = $weather['icon']            ?? '';
        $wind_speed   = (float)($weather['wind_speed']  ?? 0);

        mysqli_stmt_bind_param($stmt, 'issddiissds',
            $userId_val, $city, $country, $temperature, $feels_like,
            $humidity, $rain_chance, $condition, $icon, $wind_speed, $recommendation
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // ── PRIVATE HELPERS ────────────────────────────────────────────────────

    private function httpGet(string $url): ?array {
        // Use cURL (always available on XAMPP/WAMP/Laragon).
        // Falls back to file_get_contents if cURL is not loaded.
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_CONNECTTIMEOUT => 6,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_USERAGENT      => 'SeedCycle/1.0',
                CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            if ($response === false || $curlErr) return null;
            if ($httpCode === 401) throw new RuntimeException('Invalid API key. Please check your OpenWeatherMap API key in config/Weather.php');
            if ($httpCode === 404) return null; // city not found — caller handles this
            if ($httpCode !== 200) return null;

            $data = json_decode($response, true);
            return is_array($data) ? $data : null;
        }

        // Fallback: file_get_contents (requires allow_url_fopen = On)
        $ctx = stream_context_create([
            'http' => ['timeout' => 10, 'ignore_errors' => true],
            'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);
        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) {
            throw new RuntimeException(
                'Cannot reach the weather API. On XAMPP/WAMP, make sure the php_curl extension is enabled in php.ini.'
            );
        }
        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }

    private function extractRainChance(?array $forecast): int {
        if (!$forecast || !isset($forecast['list'][0])) return 0;
        // OWM forecast: 'pop' = probability of precipitation (0.0–1.0)
        $pop = (float)($forecast['list'][0]['pop'] ?? 0);
        return (int)round($pop * 100);
    }

    /**
     * Normalize raw OWM current-weather response into a clean array.
     */
    private function normalize(array $raw, int $rainChance): array {
        $weather = $raw['weather'][0] ?? [];
        $condId  = (int)($weather['id'] ?? 800);
        $condMain = $weather['main'] ?? 'Clear';
        $condDesc = ucfirst($weather['description'] ?? 'clear sky');
        $icon     = $weather['icon'] ?? '01d';

        // Rain volume in last 1h (mm), if present
        $rainMm = (float)($raw['rain']['1h'] ?? $raw['rain']['3h'] ?? 0);

        return [
            'city'          => $raw['name']          ?? '',
            'country'       => $raw['sys']['country'] ?? '',
            'temperature'   => round((float)($raw['main']['temp']       ?? 0), 1),
            'feels_like'    => round((float)($raw['main']['feels_like'] ?? 0), 1),
            'temp_min'      => round((float)($raw['main']['temp_min']   ?? 0), 1),
            'temp_max'      => round((float)($raw['main']['temp_max']   ?? 0), 1),
            'humidity'      => (int)($raw['main']['humidity']           ?? 0),
            'pressure'      => (int)($raw['main']['pressure']           ?? 0),
            'wind_speed'    => round((float)($raw['wind']['speed']      ?? 0), 1),
            'visibility'    => (int)($raw['visibility']                 ?? 0),
            'rain_chance'   => $rainChance,
            'rain_mm'       => $rainMm,
            'condition_id'  => $condId,
            'condition_main'=> $condMain,
            'condition_desc'=> $condDesc,
            'icon'          => $icon,
            'icon_url'      => "https://openweathermap.org/img/wn/{$icon}@2x.png",
            'sunrise'       => $raw['sys']['sunrise'] ?? 0,
            'sunset'        => $raw['sys']['sunset']  ?? 0,
        ];
    }
}
