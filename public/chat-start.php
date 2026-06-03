<?php
/**
 * chat-start.php
 * Creates or finds a direct conversation between the logged-in user
 * and another user, then redirects to the chat page.
 *
 * GET  ?with=<user_id>   — start/resume a direct chat with that user
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Helpers/ChatMigration.php';

global $conn;

\App\Helpers\ChatMigration::ensureDirectChatSchema($conn);

$myId     = (int)$_SESSION['user_id'];
$otherId  = isset($_GET['with']) ? (int)$_GET['with'] : 0;

// Validate target user
if ($otherId <= 0 || $otherId === $myId) {
    header('Location: marketplace.php');
    exit;
}

$userModel = new User($conn);
$other = $userModel->findById($otherId);
if (!$other) {
    header('Location: marketplace.php');
    exit;
}

// Normalise so buyer_id < seller_id to avoid (A,B) vs (B,A) duplicates
$buyerId  = min($myId, $otherId);
$sellerId = max($myId, $otherId);

// Check for existing direct conversation
$stmt = mysqli_prepare($conn,
    "SELECT id FROM chat_conversations
     WHERE buyer_id = ? AND seller_id = ? AND conversation_type = 'direct'
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, 'ii', $buyerId, $sellerId);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($row) {
    $convId = (int)$row['id'];
} else {
    // Create new direct conversation
    $stmt = mysqli_prepare($conn,
        "INSERT INTO chat_conversations
             (order_id, buyer_id, seller_id, conversation_type, status)
         VALUES (NULL, ?, ?, 'direct', 'active')"
    );
    mysqli_stmt_bind_param($stmt, 'ii', $buyerId, $sellerId);
    mysqli_stmt_execute($stmt);
    $convId = (int)mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
}

// Redirect to chat page with the conversation pre-selected
header("Location: chat.php?conv=$convId");
exit;
