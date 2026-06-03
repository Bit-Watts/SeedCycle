<?php

/**
 * PlantCache — local caching layer for Trefle API responses.
 * Checks DB first; only calls Trefle when data isn't cached.
 */
class PlantCache {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->ensureTable();
    }

    /**
     * Create the plant_cache table if it doesn't exist.
     */
    private function ensureTable(): void {
        mysqli_query($this->conn,
            'CREATE TABLE IF NOT EXISTS `plant_cache` (
                `id`              INT(11) NOT NULL AUTO_INCREMENT,
                `search_term`     VARCHAR(255) NOT NULL,
                `common_name`     VARCHAR(255) DEFAULT NULL,
                `scientific_name` VARCHAR(255) DEFAULT NULL,
                `plant_family`    VARCHAR(255) DEFAULT NULL,
                `plant_genus`     VARCHAR(255) DEFAULT NULL,
                `category`        VARCHAR(100) DEFAULT NULL,
                `description`     TEXT DEFAULT NULL,
                `sunlight`        VARCHAR(100) DEFAULT NULL,
                `watering`        VARCHAR(100) DEFAULT NULL,
                `soil`            VARCHAR(255) DEFAULT NULL,
                `growth_rate`     VARCHAR(100) DEFAULT NULL,
                `care_guide`      TEXT DEFAULT NULL,
                `image_url`       VARCHAR(500) DEFAULT NULL,
                `image_credit`    VARCHAR(255) DEFAULT NULL,
                `trefle_id`       INT(11) DEFAULT NULL,
                `planting_start`  TINYINT(2) DEFAULT 0,
                `planting_end`    TINYINT(2) DEFAULT 0,
                `growing_days`    INT(11) DEFAULT 0,
                `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_search_term` (`search_term`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
        // Add columns to existing tables gracefully
        @mysqli_query($this->conn, 'ALTER TABLE plant_cache ADD COLUMN IF NOT EXISTS planting_start TINYINT(2) DEFAULT 0');
        @mysqli_query($this->conn, 'ALTER TABLE plant_cache ADD COLUMN IF NOT EXISTS planting_end   TINYINT(2) DEFAULT 0');
        @mysqli_query($this->conn, 'ALTER TABLE plant_cache ADD COLUMN IF NOT EXISTS growing_days   INT(11)    DEFAULT 0');
    }

    /**
     * Look up a plant by search term in the local cache.
     */
    public function findCached(string $term): ?array {
        $term = strtolower(trim($term));
        $stmt = mysqli_prepare($this->conn,
            'SELECT * FROM plant_cache WHERE LOWER(search_term) = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 's', $term);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$row) return null;

        // If cached entry has no planting months, treat as stale — force re-fetch
        if (empty($row['planting_start']) && empty($row['planting_end'])) {
            return null;
        }

        return $row;
    }

    /**
     * Store a plant result in the cache.
     */
    public function store(string $searchTerm, array $data): bool {
        $term           = strtolower(trim($searchTerm));
        $common_name    = (string)($data['common_name']     ?? '');
        $scientific     = (string)($data['scientific_name'] ?? '');
        $plant_family   = (string)($data['plant_family']    ?? '');
        $plant_genus    = (string)($data['plant_genus']     ?? '');
        $category       = (string)($data['category']        ?? '');
        $description    = (string)($data['description']     ?? '');
        $sunlight       = (string)($data['sunlight']        ?? '');
        $watering       = (string)($data['watering']        ?? '');
        $soil           = (string)($data['soil']            ?? '');
        $growth_rate    = (string)($data['growth_rate']     ?? '');
        $care_guide     = (string)($data['care_guide']      ?? '');
        $image_url      = (string)($data['image_url']       ?? '');
        $image_credit   = (string)($data['image_credit']    ?? '');
        $trefle_id      = (int)($data['trefle_id']          ?? 0);
        $planting_start = (int)($data['planting_start']     ?? 0);
        $planting_end   = (int)($data['planting_end']       ?? 0);
        $growing_days   = (int)($data['growing_days']       ?? 0);

        $stmt = mysqli_prepare($this->conn,
            'INSERT INTO plant_cache
                (search_term, common_name, scientific_name, plant_family, plant_genus,
                 category, description, sunlight, watering, soil, growth_rate,
                 care_guide, image_url, image_credit, trefle_id,
                 planting_start, planting_end, growing_days)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                common_name     = VALUES(common_name),
                scientific_name = VALUES(scientific_name),
                plant_family    = VALUES(plant_family),
                plant_genus     = VALUES(plant_genus),
                category        = VALUES(category),
                description     = VALUES(description),
                sunlight        = VALUES(sunlight),
                watering        = VALUES(watering),
                soil            = VALUES(soil),
                growth_rate     = VALUES(growth_rate),
                care_guide      = VALUES(care_guide),
                image_url       = VALUES(image_url),
                image_credit    = VALUES(image_credit),
                trefle_id       = VALUES(trefle_id),
                planting_start  = VALUES(planting_start),
                planting_end    = VALUES(planting_end),
                growing_days    = VALUES(growing_days)'
        );
        mysqli_stmt_bind_param($stmt, 'sssssssssssssssiii',
            $term, $common_name, $scientific, $plant_family, $plant_genus,
            $category, $description, $sunlight, $watering, $soil,
            $growth_rate, $care_guide, $image_url, $image_credit, $trefle_id,
            $planting_start, $planting_end, $growing_days
        );
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}
