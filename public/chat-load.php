<?php
/**
 * chat-load.php — Load conversation messages via AJAX
 *
 * GET params:
 *   conversation_id  — required
 *   since            — optional message ID; only return messages with id > since
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/WebSocket/ChatService.php';

global $conn;

$chatService    = new \App\WebSocket\ChatService($conn);
$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
$since          = isset($_GET['since'])           ? (int)$_GET['since']           : 0;

if ($conversationId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid conversation ID']);
    exit;
}

// Get conversation with participant info
$conversation = $chatService->getConversation($conversationId);

if (!$conversation) {
    echo json_encode(['success' => false, 'error' => 'Conversation not found']);
    exit;
}

// Verify user belongs to this conversation
$myId = (int)$_SESSION['user_id'];
if ($conversation['buyer_id'] != $myId && $conversation['seller_id'] != $myId) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Get messages (optionally only those newer than $since)
$messages = $chatService->getMessages($conversationId, 100, $since);

// Mark as read (only when loading full history, not polling)
if ($since === 0) {
    $chatService->markMessagesAsRead($conversationId, $myId);
}

echo json_encode([
    'success'      => true,
    'conversation' => $conversation,
    'messages'     => $messages,
]);
