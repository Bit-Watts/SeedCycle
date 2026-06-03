<?php
/**
 * plant-lookup.php — AJAX endpoint for Perenual plant data lookup.
 * Checks local cache first, then calls Perenual API.
 * Returns JSON plant data to populate the sell/edit listing form.
 *
 * GET params:
 *   q   — plant/seed name to search
 *   lat — (optional) user latitude for location-aware month recommendations
 *   lon — (optional) user longitude
 */

// Always return JSON even on PHP errors
set_exception_handler(function($e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    exit;
});

session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$query = trim($_GET['q'] ?? '');
if (strlen($query) < 2) {
    echo json_encode(['success' => false, 'error' => 'Search term too short']);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Perenual.php';
require_once __DIR__ . '/../app/Models/PlantCache.php';

global $conn;
$cache = new PlantCache($conn);

// ── 1. Check local cache ──────────────────────────────────────────────────
$cached = $cache->findCached($query);
if ($cached) {
    echo json_encode(['success' => true, 'source' => 'cache', 'plant' => $cached]);
    exit;
}

// ── 2. Check API key ──────────────────────────────────────────────────────
if (!defined('PERENUAL_API_KEY') || PERENUAL_API_KEY === 'YOUR_PERENUAL_API_KEY_HERE') {
    echo json_encode([
        'success'  => false,
        'error'    => 'Perenual API key not configured. Open config/Perenual.php and add your key.',
        'fallback' => true,
    ]);
    exit;
}

// ── 3. cURL helper ────────────────────────────────────────────────────────
function perenual_get(string $url): string|false {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'SeedCycle/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $result = curl_exec($ch);
    $errno  = curl_errno($ch);
    curl_close($ch);
    return $errno ? false : $result;
}

// ── 4. Search Perenual species list ──────────────────────────────────────
$searchUrl = PERENUAL_API_BASE . '/species-list?key=' . urlencode(PERENUAL_API_KEY)
           . '&q=' . urlencode($query);

$response = perenual_get($searchUrl);

if ($response === false) {
    echo json_encode(['success' => false, 'error' => 'Could not reach Perenual API. Check your internet connection.']);
    exit;
}

$data = json_decode($response, true);

if (!$data || empty($data['data'])) {
    echo json_encode([
        'success' => false,
        'error'   => 'No matching plant found for "' . htmlspecialchars($query) . '". Please enter details manually.',
    ]);
    exit;
}

// ── 5. Pick best match ────────────────────────────────────────────────────
$plants     = $data['data'];
$queryLower = strtolower(trim($query));
$matched    = null;

// Pass 1: exact common_name match
foreach ($plants as $p) {
    if (strtolower(trim($p['common_name'] ?? '')) === $queryLower) {
        $matched = $p;
        break;
    }
}

// Pass 2: common_name starts with query
if (!$matched) {
    foreach ($plants as $p) {
        $cn = strtolower(trim($p['common_name'] ?? ''));
        if (strpos($cn, $queryLower) === 0) {
            $matched = $p;
            break;
        }
    }
}

// Pass 3: shortest common_name containing query (most generic variety)
if (!$matched) {
    $bestLen = PHP_INT_MAX;
    foreach ($plants as $p) {
        $cn = strtolower(trim($p['common_name'] ?? ''));
        if (strpos($cn, $queryLower) !== false && strlen($cn) < $bestLen) {
            $matched = $p;
            $bestLen = strlen($cn);
        }
    }
}

// Pass 4: fallback to first result
if (!$matched) {
    $matched = $plants[0];
}

// ── 6. Fetch full species details ─────────────────────────────────────────
$detailUrl = PERENUAL_API_BASE . '/species/details/' . (int)$matched['id']
           . '?key=' . urlencode(PERENUAL_API_KEY);
$detailRaw = perenual_get($detailUrl);
$detail    = $detailRaw ? json_decode($detailRaw, true) : null;

// Use detail data if available, fall back to search result
$d = $detail ?? $matched;

// ── 7. Map Perenual fields ────────────────────────────────────────────────

// Common name & scientific name
$commonName     = $d['common_name']      ?? ucfirst($query);
$scientificName = '';
if (!empty($d['scientific_name'])) {
    $sn = $d['scientific_name'];
    $scientificName = is_array($sn) ? implode(', ', $sn) : (string)$sn;
}

// Family
$family = (string)($d['family'] ?? '');

// Sunlight — Perenual returns array e.g. ["full sun", "part shade"]
$sunlightArr = $d['sunlight'] ?? [];
if (!is_array($sunlightArr)) $sunlightArr = [$sunlightArr];
$sunlightArr = array_filter($sunlightArr);
$sunlight = '';
if ($sunlightArr) {
    // Map to simple labels
    $joined = strtolower(implode(' ', $sunlightArr));
    if (str_contains($joined, 'full sun') && !str_contains($joined, 'shade')) {
        $sunlight = 'Full Sun';
    } elseif (str_contains($joined, 'full sun')) {
        $sunlight = 'Full Sun / Partial Shade';
    } elseif (str_contains($joined, 'part')) {
        $sunlight = 'Partial Sun';
    } elseif (str_contains($joined, 'shade')) {
        $sunlight = 'Shade';
    } else {
        $sunlight = ucwords($sunlightArr[0]);
    }
}

// Watering — Perenual returns string e.g. "Frequent", "Average", "Minimum"
$wateringRaw = (string)($d['watering'] ?? '');
$watering = match(strtolower($wateringRaw)) {
    'frequent'  => 'High',
    'average'   => 'Moderate',
    'minimum'   => 'Low',
    'none'      => 'Drought Tolerant',
    default     => $wateringRaw ? ucfirst($wateringRaw) : 'Moderate',
};

// Watering benchmark days
$waterBench = $d['watering_general_benchmark'] ?? [];
$waterDays  = '';
if (!empty($waterBench['value']) && !empty($waterBench['unit'])) {
    $waterDays = "Every {$waterBench['value']} {$waterBench['unit']}";
    $watering  = $watering . " ({$waterDays})";
}

// Soil
$soilArr = $d['soil'] ?? [];
if (!is_array($soilArr)) $soilArr = [];
$soil = !empty($soilArr) ? implode(', ', $soilArr) : 'Well-draining soil';

// Growth rate
$growthRate = (string)($d['growth_rate'] ?? '');
if (!$growthRate) {
    $cycle = strtolower($d['cycle'] ?? '');
    $growthRate = match(true) {
        str_contains($cycle, 'annual') => 'Fast',
        str_contains($cycle, 'biennial') => 'Moderate',
        default => '',
    };
}

// Maintenance / care level
$maintenance = (string)($d['maintenance'] ?? $d['care_level'] ?? '');

// Description
$description = (string)($d['description'] ?? '');

// Care guide — build from available data
$carePoints = array_filter([
    $sunlight    ? "☀️ Sunlight: {$sunlight}"      : '',
    $watering    ? "💧 Watering: {$watering}"      : '',
    $soil        ? "🌱 Soil: {$soil}"              : '',
    $growthRate  ? "📈 Growth Rate: {$growthRate}" : '',
    $maintenance ? "🔧 Maintenance: {$maintenance}": '',
]);
$careGuide = implode("\n", $carePoints);

// Image
$imageUrl    = '';
$imageCredit = '';
if (!empty($d['default_image']['regular_url'])) {
    $imageUrl = $d['default_image']['regular_url'];
} elseif (!empty($d['default_image']['original_url'])) {
    $imageUrl = $d['default_image']['original_url'];
} elseif (!empty($d['default_image']['medium_url'])) {
    $imageUrl = $d['default_image']['medium_url'];
}
if (!empty($d['default_image']['license_name'])) {
    $imageCredit = $d['default_image']['license_name'];
}

// Category mapping from family
$catMap = [
    'solanaceae'     => 'Vegetable',
    'cucurbitaceae'  => 'Vegetable',
    'brassicaceae'   => 'Vegetable',
    'fabaceae'       => 'Vegetable',
    'asteraceae'     => 'Vegetable',
    'apiaceae'       => 'Herb',
    'lamiaceae'      => 'Herb',
    'poaceae'        => 'Grain',
    'rosaceae'       => 'Fruit',
    'rutaceae'       => 'Fruit',
    'musaceae'       => 'Fruit',
    'myrtaceae'      => 'Fruit',
    'moraceae'       => 'Fruit',
    'anacardiaceae'  => 'Fruit',
    'liliaceae'      => 'Flower',
    'asteraceae'     => 'Flower',
    'zingiberaceae'  => 'Herb',
    'convolvulaceae' => 'Vegetable',
    'araceae'        => 'Vegetable',
];
$category = $catMap[strtolower($family)] ?? 'Vegetable';

// Also check Perenual's type field
$typeRaw = strtolower($d['type'] ?? '');
if (!isset($catMap[strtolower($family)])) {
    if (str_contains($typeRaw, 'herb'))   $category = 'Herb';
    elseif (str_contains($typeRaw, 'fruit')) $category = 'Fruit';
    elseif (str_contains($typeRaw, 'flower')) $category = 'Flower';
    elseif (str_contains($typeRaw, 'grain') || str_contains($typeRaw, 'grass')) $category = 'Grain';
}

// ── 8. Planting months ────────────────────────────────────────────────────
// Perenual: use pruning_month as a proxy or fall back to location-based defaults
$userLat = isset($_GET['lat']) ? (float)$_GET['lat'] : null;
$userLon = isset($_GET['lon']) ? (float)$_GET['lon'] : null;

$isPhilippines = ($userLat !== null && $userLat >= 4 && $userLat <= 21
                  && $userLon !== null && $userLon >= 116 && $userLon <= 127);
$isTropical    = ($userLat !== null && abs($userLat) <= 23.5);

$plantingStart = 0;
$plantingEnd   = 0;

// Perenual doesn't have a direct "planting months" field —
// use Philippine/tropical fallbacks based on family
$monthDefaults = ($isPhilippines || $isTropical) ? [
    'solanaceae'     => [2, 4],
    'cucurbitaceae'  => [11, 1],
    'brassicaceae'   => [10, 1],
    'fabaceae'       => [10, 12],
    'apiaceae'       => [10, 1],
    'lamiaceae'      => [3, 6],
    'poaceae'        => [6, 7],
    'rosaceae'       => [11, 1],
    'musaceae'       => [4, 6],
    'rutaceae'       => [3, 5],
    'myrtaceae'      => [3, 5],
    'moraceae'       => [3, 5],
    'zingiberaceae'  => [4, 6],
    'convolvulaceae' => [6, 8],
    'araceae'        => [5, 7],
    'asteraceae'     => [10, 12],
    'anacardiaceae'  => [3, 5],
] : [
    'solanaceae'    => [4, 6],
    'cucurbitaceae' => [5, 7],
    'brassicaceae'  => [3, 5],
    'fabaceae'      => [4, 6],
    'apiaceae'      => [3, 5],
    'lamiaceae'     => [4, 7],
    'poaceae'       => [4, 6],
    'rosaceae'      => [3, 5],
    'myrtaceae'     => [4, 6],
];

$def = $monthDefaults[strtolower($family)] ?? [0, 0];
$plantingStart = $def[0];
$plantingEnd   = $def[1];

// ── 9. Growing days ───────────────────────────────────────────────────────
$growingDays = 0;
$dayDefaults = [
    'solanaceae'     => 75,
    'cucurbitaceae'  => 60,
    'brassicaceae'   => 60,
    'fabaceae'       => 55,
    'apiaceae'       => 70,
    'lamiaceae'      => 40,
    'poaceae'        => 90,
    'rosaceae'       => 120,
    'myrtaceae'      => 120,
    'moraceae'       => 150,
    'musaceae'       => 365,
    'zingiberaceae'  => 210,
    'anacardiaceae'  => 120,
];
$growingDays = $dayDefaults[strtolower($family)] ?? 0;

// ── 10. Build response ────────────────────────────────────────────────────
$plantData = [
    'perenual_id'     => (int)($d['id'] ?? 0),
    'common_name'     => $commonName,
    'scientific_name' => $scientificName,
    'plant_family'    => $family,
    'plant_genus'     => (string)($d['genus'] ?? ''),
    'category'        => $category,
    'description'     => $description,
    'sunlight'        => $sunlight,
    'watering'        => $watering,
    'soil'            => $soil,
    'growth_rate'     => $growthRate,
    'care_guide'      => $careGuide,
    'image_url'       => $imageUrl,
    'image_credit'    => $imageCredit,
    'planting_start'  => $plantingStart,
    'planting_end'    => $plantingEnd,
    'growing_days'    => $growingDays,
    'cycle'           => (string)($d['cycle'] ?? ''),
    'maintenance'     => $maintenance,
];

// ── 11. Store in cache ────────────────────────────────────────────────────
// Map perenual_id to trefle_id field for cache compatibility
$plantData['trefle_id'] = $plantData['perenual_id'];
$cache->store($query, $plantData);

echo json_encode(['success' => true, 'source' => 'api', 'plant' => $plantData]);
