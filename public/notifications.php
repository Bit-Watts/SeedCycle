<?php
/**
 * Notifications Page - View all order notifications
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/WebSocket/ChatService.php';

global $conn;

$userModel = new User($conn);
$chatService = new \App\WebSocket\ChatService($conn);

$user = $userModel->findById($_SESSION['user_id']);

if (!$user) {
    header('Location: login.php');
    exit;
}

// Mark notification as read if requested
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $notificationId = (int)$_GET['id'];
    $chatService->markNotificationAsRead($notificationId);
    header('Location: notifications.php');
    exit;
}

// Get user's notifications
$notifications = $chatService->getUserNotifications($_SESSION['user_id'], 50);

require __DIR__ . '/../app/Views/notifications.php';
