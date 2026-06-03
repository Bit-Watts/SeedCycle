<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Seed.php';
require_once __DIR__ . '/../Models/SeedListing.php';

class SeedController {

    private User        $userModel;
    private Seed        $seedModel;
    private SeedListing $listingModel;

    public function __construct() {
        global $conn;
        $this->userModel    = new User($conn);
        $this->seedModel    = new Seed($conn);
        $this->listingModel = new SeedListing($conn);
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        
        // Redirect admin users to admin panel
        if (($_SESSION['role'] ?? 'user') === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
        }
    }

    public function marketplace(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $seeds = $this->seedModel->getAll();

        // Pre-format month ranges so the view doesn't need the model
        foreach ($seeds as &$seed) {
            $startM = Seed::monthName($seed['planting_start_month'] ?? null);
            $endM   = Seed::monthName($seed['planting_end_month']   ?? null);
            $seed['month_range'] = $startM && $endM ? "$startM – $endM" : ($startM ?: '');
        }
        unset($seed);

        // Get IDs of seeds owned by the logged-in user so we can hide Add to Cart
        $ownedSeedIds = [];
        if (isset($_SESSION['user_id'])) {
            global $conn;
            $stmt = mysqli_prepare($conn,
                'SELECT inventory_id FROM seed_listings WHERE user_id = ?'
            );
            mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($res)) {
                $ownedSeedIds[] = (int)$row['inventory_id'];
            }
            mysqli_stmt_close($stmt);
        }

        // Show error if user tried to buy own seed
        $ownSeedError = isset($_GET['error']) && $_GET['error'] === 'own_seed';

        require __DIR__ . '/../Views/marketplace.php';
    }

    public function details(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        global $conn;
        require_once __DIR__ . '/../Models/Review.php';
        $reviewModel = new Review($conn);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            header('Location: marketplace.php');
            exit;
        }

        $seed = $this->seedModel->findById($id);
        if (!$seed) {
            header('Location: marketplace.php');
            exit;
        }

        // Pre-format month range
        $startM = Seed::monthName($seed['planting_start_month'] ?? null);
        $endM   = Seed::monthName($seed['planting_end_month']   ?? null);
        $seed['month_range'] = $startM && $endM ? "$startM – $endM" : ($startM ?: '');

        // Get seller information
        $stmt = mysqli_prepare($conn,
            'SELECT u.id, u.first_name, u.last_name, u.email, u.profile_image, u.address, u.created_at,
                    sl.created_at as listing_date
             FROM seed_listings sl
             JOIN users u ON u.id = sl.user_id
             WHERE sl.inventory_id = ? AND sl.status = "approved"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $sellerInfo = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        // Get seller's total listings count
        if ($sellerInfo) {
            $stmt = mysqli_prepare($conn,
                'SELECT COUNT(*) as total FROM seed_listings WHERE user_id = ? AND status = "approved"'
            );
            mysqli_stmt_bind_param($stmt, 'i', $sellerInfo['id']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            $sellerInfo['total_listings'] = $result['total'];
            mysqli_stmt_close($stmt);
        }

        // Check if this seed belongs to the logged-in user
        $isOwnSeed = isset($_SESSION['user_id'])
            ? $this->seedModel->isOwnedBy($id, $_SESSION['user_id'])
            : false;

        // Reviews
        $reviews       = $reviewModel->getByInventory($id);
        $ratingData    = $reviewModel->getAverageRating($id);
        $hasReviewed   = isset($_SESSION['user_id']) ? $reviewModel->hasReviewed($_SESSION['user_id'], $id) : false;
        $canReview     = isset($_SESSION['user_id']) && !$isOwnSeed && !$hasReviewed
                         && $reviewModel->hasPurchased($_SESSION['user_id'], $id);

        // Flash messages
        $reviewError   = $_GET['review_error']   ?? null;
        $reviewSuccess = isset($_GET['review_success']);
        $reportSuccess = isset($_GET['report_success']);
        $reportError   = $_GET['report_error']   ?? null;

        require __DIR__ . '/../Views/seeds/details.php';
    }

    public function mySeeds(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $user    = $this->userModel->findById($_SESSION['user_id']);
        $message = null;
        $error   = null;

        // Handle remove listing POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_listing'])) {
            $inventoryId = (int)($_POST['inventory_id'] ?? 0);
            if ($this->listingModel->removeListing($inventoryId, $_SESSION['user_id'])) {
                $message = 'Listing removed successfully.';
            } else {
                $error = 'Failed to remove listing.';
            }
        }

        // Handle add stock POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stock'])) {
            $inventoryId = (int)($_POST['inventory_id'] ?? 0);
            $qty         = (int)($_POST['qty']          ?? 0);

            if ($qty < 1) {
                $error = 'Quantity must be at least 1.';
            } elseif ($this->listingModel->addStock($inventoryId, $_SESSION['user_id'], $qty)) {
                $message = "Stock updated successfully.";
            } else {
                $error = 'Failed to update stock.';
            }
        }

        $seeds = $this->listingModel->getByUser($_SESSION['user_id']);

        require __DIR__ . '/../Views/my-seeds.php';
    }

    public function plantingGuide(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        global $conn;

        $result = mysqli_query($conn,
            'SELECT id, name, category, planting_start_month, planting_end_month, growing_days
             FROM inventory
             WHERE is_active = 1 AND planting_start_month IS NOT NULL
             ORDER BY planting_start_month ASC, name ASC'
        );
        $plantingSeeds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['start_month_name'] = Seed::monthName($row['planting_start_month']);
            $row['end_month_name']   = Seed::monthName($row['planting_end_month']);
            $plantingSeeds[] = $row;
        }

        require __DIR__ . '/../Views/planting-guide.php';
    }

    public function editListing(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        global $conn;
        $inventoryId = (int)($_GET['id'] ?? $_POST['inventory_id'] ?? 0);

        if ($inventoryId <= 0) {
            header('Location: profile.php');
            exit;
        }

        $listing = $this->listingModel->getByInventoryId($inventoryId, $_SESSION['user_id']);
        if (!$listing) {
            header('Location: profile.php');
            exit;
        }

        // Capture where the user came from for the back button
        // On GET: store the referrer. On POST: carry it through via hidden field.
        $allowedBack = ['profile.php', 'my-seeds.php', 'marketplace.php', 'index.php', 'dashboard.php'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $backUrl = $_POST['back_url'] ?? 'profile.php';
        } else {
            $backUrl = 'profile.php';
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $refFile = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
                if (in_array($refFile, $allowedBack)) {
                    $backUrl = $refFile;
                }
            }
        }
        if (!in_array(strtok($backUrl, '?'), $allowedBack)) {
            $backUrl = 'profile.php';
        }

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seedName    = trim($_POST['seed_name']   ?? '');
            $category    = trim($_POST['category']    ?? '');
            $price       = (float)($_POST['price']    ?? 0);
            $description = trim($_POST['description'] ?? '');
            $startMonth  = (int)($_POST['planting_start_month'] ?? 0) ?: null;
            $endMonth    = (int)($_POST['planting_end_month']   ?? 0) ?: null;
            $growingDays = (int)($_POST['growing_days'] ?? 0) ?: null;
            $stockQty    = max(0, (int)($_POST['stock_quantity'] ?? 0));

            $allowedCategories = ['Vegetable','Herb','Fruit','Flower','Grain','Other'];

            if (!$seedName) {
                $error = 'Seed name is required.';
            } elseif ($price <= 0) {
                $error = 'Price must be a positive number.';
            } elseif ($category && !in_array($category, $allowedCategories, true)) {
                $error = 'Invalid category selected.';
            } else {
                // Handle image upload if new image provided
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = __DIR__ . '/../../public/assets/uploads/listings/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','webp'];
                    if (!in_array($ext, $allowed)) {
                        $error = 'Image must be JPG, PNG, or WEBP.';
                    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                        $error = 'Image must be under 2MB.';
                    } else {
                        $filename = 'listing_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                        $imageUrl = 'assets/uploads/listings/' . $filename;
                        $this->listingModel->updateImage($inventoryId, $_SESSION['user_id'], $imageUrl);
                    }
                }

                if (!$error) {
                    $ok = $this->listingModel->updateListing(
                        $inventoryId, $_SESSION['user_id'],
                        $seedName, $category, $price, $description,
                        $startMonth, $endMonth, $growingDays, $stockQty
                    );
                    if ($ok) {
                        $success = 'Listing updated successfully.';
                        $listing = $this->listingModel->getByInventoryId($inventoryId, $_SESSION['user_id']);
                    } else {
                        $error = 'Failed to update listing. Please try again.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/seeds/edit.php';
    }

    public function sell(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        global $conn;
        $user       = $this->userModel->findById($_SESSION['user_id']);
        $error      = null;
        $success    = null;
        $myListings = $this->listingModel->getByUser($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $seedName    = trim($_POST['seed_name']   ?? '');
            $category    = trim($_POST['category']    ?? '');
            $price       = (float)($_POST['price']    ?? 0);
            $description = trim($_POST['description'] ?? '');
            $startMonth  = (int)($_POST['planting_start_month'] ?? 0) ?: null;
            $endMonth    = (int)($_POST['planting_end_month']   ?? 0) ?: null;
            $growingDays = (int)($_POST['growing_days'] ?? 0) ?: null;
            $stockQty    = max(1, (int)($_POST['stock_quantity'] ?? 1));

            // Trefle auto-filled extras
            $scientificName = trim($_POST['scientific_name'] ?? '');
            $plantFamily    = trim($_POST['plant_family']    ?? '');
            $trefleImageUrl = trim($_POST['trefle_image_url'] ?? '');

            $allowedCategories = ['Vegetable','Herb','Fruit','Flower','Grain','Other'];

            if (!$seedName) {
                $error = 'Seed name is required.';
            } elseif ($price <= 0) {
                $error = 'Price must be a positive number.';
            } elseif ($stockQty < 1) {
                $error = 'Stock quantity must be at least 1.';
            } elseif ($category && !in_array($category, $allowedCategories, true)) {
                $error = 'Invalid category selected.';
            } elseif ($this->listingModel->alreadyPendingByName($_SESSION['user_id'], $seedName)) {
                $error = 'You already have a pending request for a seed with this name.';
            } else {
                // Handle image upload (manual takes priority over Trefle image)
                $imageUrl = '';
                if (!empty($_FILES['image']['name'])) {
                    $uploadDir = __DIR__ . '/../../public/assets/uploads/listings/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','webp'];
                    if (!in_array($ext, $allowed)) {
                        $error = 'Image must be JPG, PNG, or WEBP.';
                    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                        $error = 'Image must be under 2MB.';
                    } else {
                        $filename = 'listing_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                        $imageUrl = 'assets/uploads/listings/' . $filename;
                    }
                } elseif ($trefleImageUrl) {
                    // Download and save the Perenual/external image locally
                    $imageUrl = $this->downloadExternalImage($trefleImageUrl, $_SESSION['user_id']);
                    if (!$imageUrl) {
                        // Fallback: store the URL directly if download fails
                        $imageUrl = $trefleImageUrl;
                    }
                }

                if (!$error) {
                    // Enrich description with scientific name if available
                    if ($scientificName && !str_contains($description, $scientificName)) {
                        $description = $description ? $description . "\n\nScientific name: " . $scientificName : "Scientific name: " . $scientificName;
                    }

                    $result = $this->listingModel->createWithInventory(
                        $_SESSION['user_id'], $seedName, $category, $price,
                        $description, $startMonth, $endMonth, $growingDays, $stockQty, $imageUrl
                    );
                    if ($result !== false) {
                        // Store extended plant details if provided
                        if ($plantFamily || $scientificName) {
                            $this->storePlantDetails($conn, $result, $_POST);
                        }
                        $success    = 'Your listing has been submitted and is pending admin approval.';
                        $myListings = $this->listingModel->getByUser($_SESSION['user_id']);
                    } else {
                        $error = 'Failed to submit request. Please try again.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/seeds/sell.php';
    }

    /**
     * Download an external image (e.g. from Perenual) and save it locally.
     * Returns the local relative path, or empty string on failure.
     */
    private function downloadExternalImage(string $url, int $userId): string {
        if (empty($url) || (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://'))) {
            return '';
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/listings/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Determine extension from URL
        $urlPath = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo($urlPath ?? '', PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            $ext = 'jpg'; // default
        }

        $filename = 'listing_' . $userId . '_' . time() . '.' . $ext;
        $savePath = $uploadDir . $filename;

        // Download with cURL
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => 'SeedCycle/1.0',
        ]);
        $imageData = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$imageData || $httpCode !== 200 || strlen($imageData) < 1000) {
            return ''; // too small or failed
        }

        if (file_put_contents($savePath, $imageData) === false) {
            return '';
        }

        return 'assets/uploads/listings/' . $filename;
    }

    /**
     * Store extended plant details in inventory columns.
     */
    private function storePlantDetails($conn, int $inventoryId, array $post): void {
        // Gracefully add columns if they don't exist yet
        @mysqli_query($conn, 'ALTER TABLE inventory
            ADD COLUMN IF NOT EXISTS scientific_name VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS plant_family    VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS sunlight        VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS watering        VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS soil_preference VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS growth_rate     VARCHAR(100) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS care_guide      TEXT         DEFAULT NULL'
        );

        $stmt = mysqli_prepare($conn,
            'UPDATE inventory SET
                scientific_name = ?,
                plant_family    = ?,
                sunlight        = ?,
                watering        = ?,
                soil_preference = ?,
                growth_rate     = ?,
                care_guide      = ?
             WHERE id = ?'
        );
        if (!$stmt) return;

        $sn = trim($post['scientific_name'] ?? '');
        $pf = trim($post['plant_family']    ?? '');
        $sl = trim($post['sunlight']        ?? '');
        $wa = trim($post['watering']        ?? '');
        $so = trim($post['soil']            ?? '');
        $gr = trim($post['growth_rate']     ?? '');
        $cg = trim($post['care_guide']      ?? '');

        mysqli_stmt_bind_param($stmt, 'sssssssi', $sn, $pf, $sl, $wa, $so, $gr, $cg, $inventoryId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
