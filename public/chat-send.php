<?php
/**
 * chat-send.php — HTTP fallback for sending messages
 * Used when the WebSocket server is not running.
 * POST: conversation_id, message
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'POST required']);
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/WebSocket/ChatService.php';

global $conn;

$chatService    = new \App\WebSocket\ChatService($conn);
$conversationId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;
$message        = trim($_POST['message'] ?? '');

if ($conversationId <= 0 || $message === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// Verify user belongs to this conversation
$conv = $chatService->getConversation($conversationId);
if (!$conv) {
    echo json_encode(['success' => false, 'error' => 'Conversation not found']);
    exit;
}

$myId = (int)$_SESSION['user_id'];
if ($conv['buyer_id'] != $myId && $conv['seller_id'] != $myId) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Prevent empty / too-long messages
if (mb_strlen($message) > 2000) {
    echo json_encode(['success' => false, 'error' => 'Message too long']);
    exit;
}

$msgId = $chatService->saveMessage($conversationId, $myId, $message, 'text');

if ($msgId) {
    echo json_encode([
        'success'    => true,
        'message_id' => $msgId,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save message']);
}
