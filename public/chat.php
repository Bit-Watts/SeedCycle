<?php
/**
 * Chat Page — real-time messaging (order-based + direct)
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/WebSocket/ChatService.php';
require_once __DIR__ . '/../app/Helpers/ChatMigration.php';

global $conn;

\App\Helpers\ChatMigration::ensureDirectChatSchema($conn);

$userModel   = new User($conn);
$chatService = new \App\WebSocket\ChatService($conn);

$user = $userModel->findById($_SESSION['user_id']);
if (!$user) {
    header('Location: login.php');
    exit;
}

// Pre-selected conversation from ?conv=<id>
$openConvId = isset($_GET['conv']) ? (int)$_GET['conv'] : 0;

// Get all conversations for the sidebar
$conversations = $chatService->getUserConversations($_SESSION['user_id']);

require __DIR__ . '/../app/Views/chat.php';
