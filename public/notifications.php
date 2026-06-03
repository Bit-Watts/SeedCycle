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
    // Redirect back to the page the user came from before notifications, not back to this page
    $ref = $_GET['ref'] ?? '';
    $allowed = ['index.php','dashboard.php','orders.php','marketplace.php','profile.php',
                'seller-orders.php','order-tracking.php','planting-guide.php','my-seeds.php'];
    $redirect = 'notifications.php';
    if ($ref && in_array(strtok($ref, '?'), $allowed)) {
        $redirect = htmlspecialchars_decode($ref);
    }
    header('Location: ' . $redirect);
    exit;
}

// Store the referrer so the view can use it for the back button
$backUrl = 'index.php';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $refPath = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $refFile = basename($refPath);
    $allowed = ['index.php','dashboard.php','orders.php','marketplace.php','profile.php',
                'seller-orders.php','order-tracking.php','planting-guide.php','my-seeds.php'];
    if (in_array($refFile, $allowed)) {
        $backUrl = $refFile;
    }
}

// Get user's notifications
$notifications = $chatService->getUserNotifications($_SESSION['user_id'], 50);

require __DIR__ . '/../app/Views/notifications.php';
