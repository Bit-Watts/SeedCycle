<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/TwoFactor.php';
require_once __DIR__ . '/../Models/User.php';

class TwoFactorController {

    private TwoFactor $tfModel;
    private User      $userModel;

    public function __construct() {
        global $conn;
        $this->tfModel   = new TwoFactor($conn);
        $this->userModel = new User($conn);
    }

    private function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
    }

    /** Complete login and set all session variables */
    private function completeLogin(int $userId): void {
        $user = $this->userModel->findById($userId);
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['first_name']    = $user['first_name'];
        $_SESSION['last_name']     = $user['last_name'];
        $_SESSION['username']      = $user['username'];
        $_SESSION['email']         = $user['email'];
        $_SESSION['role']          = $user['role'] ?? 'user';
        $_SESSION['profile_image'] = $user['profile_image'] ?? '';
        $_SESSION['2fa_verified']  = true;

        // Clean up pending session keys
        unset($_SESSION['2fa_pending_user_id'],
              $_SESSION['2fa_pending_email'],
              $_SESSION['2fa_pending_role']);

        if (($user['role'] ?? 'user') === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }

    /**
     * 2FA Setup — shown after login if user has no totp_secret yet.
     * Steps: generate secret → show QR → verify code → enable → complete login.
     */
    public function setup(): void {
        $this->startSession();

        // Must come from login flow OR be already logged in (settings page)
        $pendingUserId = $_SESSION['2fa_pending_user_id'] ?? $_SESSION['user_id'] ?? null;
        if (!$pendingUserId) {
            header('Location: login.php');
            exit;
        }

        $userId = (int)$pendingUserId;
        $user   = $this->userModel->findById($userId);
        $status = $this->tfModel->getStatus($userId);

        $secret  = null;
        $qrCode  = null;
        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'generate') {
                // Generate new secret, save it, show QR
                $secret = $this->tfModel->generateSecret();
                $this->tfModel->saveSecret($userId, $secret);
                $qrCode = $this->tfModel->getQRCodeSvg($user['email'], $secret);

            } elseif ($action === 'verify_enable') {
                $code   = trim($_POST['code'] ?? '');
                $status = $this->tfModel->getStatus($userId);
                $secret = $status['totp_secret'];

                if (!$secret) {
                    $error = 'Please generate a QR code first.';
                } elseif (!$this->tfModel->verify($secret, $code)) {
                    $error  = 'Invalid code. Please try again.';
                    $qrCode = $this->tfModel->getQRCodeSvg($user['email'], $secret);
                } else {
                    // Enable 2FA and complete login
                    $this->tfModel->enable($userId);

                    // If coming from login flow, complete login now
                    if (isset($_SESSION['2fa_pending_user_id'])) {
                        $this->completeLogin($userId);
                    } else {
                        $success = '2FA has been enabled successfully!';
                        $status  = $this->tfModel->getStatus($userId);
                    }
                }

            } elseif ($action === 'disable') {
                // Only available from settings (user already logged in)
                if (!isset($_SESSION['user_id'])) {
                    header('Location: login.php');
                    exit;
                }
                $code = trim($_POST['code'] ?? '');
                $st   = $this->tfModel->getStatus($userId);

                if (!$this->tfModel->verify($st['totp_secret'], $code)) {
                    $error = 'Invalid code. 2FA not disabled.';
                } else {
                    $this->tfModel->disable($userId);
                    unset($_SESSION['2fa_verified']);
                    $success = '2FA has been disabled.';
                    $status  = $this->tfModel->getStatus($userId);
                }
            }
        }

        // If secret exists but not yet enabled, show QR again
        if (!$qrCode && !empty($status['totp_secret']) && !$status['totp_enabled']) {
            $secret = $status['totp_secret'];
            $qrCode = $this->tfModel->getQRCodeSvg($user['email'], $secret);
        }

        // Determine if this is a login-flow setup or settings-page setup
        $isLoginFlow = isset($_SESSION['2fa_pending_user_id']);

        require __DIR__ . '/../Views/two-factor.php';
    }

    /**
     * 2FA Verify — shown after login if user already has totp_enabled = 1.
     * Enter code → complete login.
     */
    public function verify(): void {
        $this->startSession();

        if (!isset($_SESSION['2fa_pending_user_id'])) {
            header('Location: login.php');
            exit;
        }

        $userId = (int)$_SESSION['2fa_pending_user_id'];
        $error  = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code   = trim($_POST['code'] ?? '');
            $status = $this->tfModel->getStatus($userId);

            if ($this->tfModel->verify($status['totp_secret'], $code)) {
                $this->completeLogin($userId);
            } else {
                $error = 'Invalid code. Please try again.';
            }
        }

        require __DIR__ . '/../Views/two-factor-verify.php';
    }
}
