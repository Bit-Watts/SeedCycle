<?php
session_start();
require_once '../config/Database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = (int)($_GET['order_id'] ?? 0);
$amount = (float)($_GET['amount'] ?? 0);

if ($orderId <= 0 || $amount <= 0) {
    header('Location: orders.php');
    exit;
}

// Verify order belongs to user
$stmt = mysqli_prepare($conn, 
    'SELECT id, total_amount, payment_status FROM orders WHERE id = ? AND user_id = ?'
);
mysqli_stmt_bind_param($stmt, 'ii', $orderId, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    header('Location: orders.php');
    exit;
}

if ($order['payment_status'] === 'paid') {
    header('Location: payment-success.php?order_id=' . $orderId);
    exit;
}

require_once '../app/Views/payment-gcash.php';
