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

    public function sell(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

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
                // Handle image upload
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
                }

                if (!$error) {
                    $result = $this->listingModel->createWithInventory(
                        $_SESSION['user_id'], $seedName, $category, $price,
                        $description, $startMonth, $endMonth, $growingDays, $stockQty, $imageUrl
                    );
                    if ($result !== false) {
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
}
