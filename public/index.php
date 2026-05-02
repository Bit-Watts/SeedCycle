<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: landing.php');
    exit;
}
require_once '../app/Controllers/DashboardController.php';
(new DashboardController())->overview();
