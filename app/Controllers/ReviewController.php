<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Review.php';
require_once __DIR__ . '/../Models/Report.php';
require_once __DIR__ . '/../Models/User.php';

class ReviewController {

    private Review $reviewModel;
    private Report $reportModel;
    private User   $userModel;

    public function __construct() {
        global $conn;
        $this->reviewModel = new Review($conn);
        $this->reportModel = new Report($conn);
        $this->userModel   = new User($conn);
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    /** Submit a review for a seed (POST from seed details page) */
    public function submit(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $inventoryId = (int)($_POST['inventory_id'] ?? 0);
        $rating      = (int)($_POST['rating']       ?? 0);
        $comment     = trim($_POST['comment']        ?? '');

        if ($inventoryId <= 0 || $rating < 1 || $rating > 5) {
            header("Location: seed-details.php?id={$inventoryId}&review_error=invalid");
            exit;
        }

        if ($this->reviewModel->hasReviewed($_SESSION['user_id'], $inventoryId)) {
            header("Location: seed-details.php?id={$inventoryId}&review_error=already");
            exit;
        }

        if (!$this->reviewModel->hasPurchased($_SESSION['user_id'], $inventoryId)) {
            header("Location: seed-details.php?id={$inventoryId}&review_error=not_purchased");
            exit;
        }

        $orderId = $this->reviewModel->getOrderIdForReview($_SESSION['user_id'], $inventoryId);
        $this->reviewModel->create($_SESSION['user_id'], $inventoryId, $orderId, $rating, $comment);

        header("Location: seed-details.php?id={$inventoryId}&review_success=1");
        exit;
    }

    /** Delete own review */
    public function delete(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $reviewId    = (int)($_POST['review_id']    ?? 0);
        $inventoryId = (int)($_POST['inventory_id'] ?? 0);

        $this->reviewModel->delete($reviewId, $_SESSION['user_id']);
        header("Location: seed-details.php?id={$inventoryId}");
        exit;
    }

    /** Report a seed or review */
    public function report(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $type     = $_POST['type']      ?? '';
        $targetId = (int)($_POST['target_id'] ?? 0);
        $reason   = trim($_POST['reason']     ?? '');
        $details  = trim($_POST['details']    ?? '');
        $redirect = $_POST['redirect']  ?? 'marketplace.php';

        $allowedTypes   = ['seed', 'review'];
        $allowedReasons = ['spam', 'fake', 'inappropriate', 'wrong_info', 'other'];

        if (!in_array($type, $allowedTypes) || $targetId <= 0 || !in_array($reason, $allowedReasons)) {
            header("Location: {$redirect}&report_error=invalid");
            exit;
        }

        if ($this->reportModel->alreadyReported($_SESSION['user_id'], $type, $targetId)) {
            header("Location: {$redirect}&report_error=already");
            exit;
        }

        $this->reportModel->create($_SESSION['user_id'], $type, $targetId, $reason, $details);
        header("Location: {$redirect}&report_success=1");
        exit;
    }
}
