<?php
/**
 * Public Seller Profile Page
 * View seller information and their listings
 */

session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Seed.php';
require_once __DIR__ . '/../app/Helpers/ChatMigration.php';

global $conn;

\App\Helpers\ChatMigration::ensureDirectChatSchema($conn);

$sellerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($sellerId <= 0) {
    header('Location: marketplace.php');
    exit;
}

// Get seller information
$userModel = new User($conn);
$seller = $userModel->findById($sellerId);

if (!$seller) {
    header('Location: marketplace.php');
    exit;
}

// Get seller's approved listings
$stmt = mysqli_prepare($conn,
    'SELECT i.*, sl.created_at as listing_date, sl.status
     FROM seed_listings sl
     JOIN inventory i ON i.id = sl.inventory_id
     WHERE sl.user_id = ? AND sl.status = "approved" AND i.is_active = 1
     ORDER BY sl.created_at DESC'
);
mysqli_stmt_bind_param($stmt, 'i', $sellerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$sellerListings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $sellerListings[] = $row;
}
mysqli_stmt_close($stmt);

// Get seller statistics
$stmt = mysqli_prepare($conn,
    'SELECT
        COUNT(DISTINCT sl.id) as total_listings,
        COUNT(DISTINCT oi.order_id) as total_sales,
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(DISTINCT r.id) as total_reviews
     FROM seed_listings sl
     LEFT JOIN order_items oi ON oi.inventory_id = sl.inventory_id
     LEFT JOIN orders o ON o.id = oi.order_id AND o.status = "delivered"
     LEFT JOIN reviews r ON r.inventory_id = sl.inventory_id
     WHERE sl.user_id = ? AND sl.status = "approved"'
);
mysqli_stmt_bind_param($stmt, 'i', $sellerId);
mysqli_stmt_execute($stmt);
$sellerStats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Format month ranges for listings
foreach ($sellerListings as &$listing) {
    $startM = Seed::monthName($listing['planting_start_month'] ?? null);
    $endM   = Seed::monthName($listing['planting_end_month']   ?? null);
    $listing['month_range'] = $startM && $endM ? "$startM – $endM" : ($startM ?: '');
}
unset($listing);

// Get current user info if logged in
$user    = null;
$canChat = false;
$existingConvId = null;

if (isset($_SESSION['user_id'])) {
    $user    = $userModel->findById($_SESSION['user_id']);
    $myId    = (int)$_SESSION['user_id'];
    $canChat = ($myId !== $sellerId); // can't chat with yourself

    if ($canChat) {
        // Check if a direct conversation already exists
        $buyerId    = min($myId, $sellerId);
        $sellerNorm = max($myId, $sellerId);
        $stmt = mysqli_prepare($conn,
            "SELECT id FROM chat_conversations
             WHERE buyer_id = ? AND seller_id = ? AND conversation_type = 'direct'
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 'ii', $buyerId, $sellerNorm);
        mysqli_stmt_execute($stmt);
        $convRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        $existingConvId = $convRow ? (int)$convRow['id'] : null;
    }
}

require __DIR__ . '/../app/Views/seller-profile.php';
