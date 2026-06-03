<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: landing.php');
    exit;
}

// Redirect admin users to admin panel
if (($_SESSION['role'] ?? 'user') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

require_once '../app/Controllers/DashboardController.php';
(new DashboardController())->overview();
