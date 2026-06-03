<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Order.php';

class DashboardController {

    private User  $userModel;
    private Order $orderModel;

    public function __construct() {
        global $conn;
        $this->userModel  = new User($conn);
        $this->orderModel = new Order($conn);
    }

    private function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
        
        // Redirect admin users to admin panel
        if (($_SESSION['role'] ?? 'user') === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
        }
    }

    public function overview(): void {
        $this->requireAuth();
        global $conn;

        $user = $this->userModel->findById($_SESSION['user_id']);
        if (!$user) { $this->sessionExpired(); }

        $userId = $_SESSION['user_id'];

        // Real stat: orders placed by user
        $stmt = mysqli_prepare($conn, 'SELECT COUNT(*) AS cnt FROM orders WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $ordersCount = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['cnt'] ?? 0);
        mysqli_stmt_close($stmt);

        // Real stat: seed listings by user
        $stmt2 = mysqli_prepare($conn, 'SELECT COUNT(*) AS cnt FROM seed_listings WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt2, 'i', $userId);
        mysqli_stmt_execute($stmt2);
        $listingsCount = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['cnt'] ?? 0);
        mysqli_stmt_close($stmt2);

        // Real stat: orders received as seller (orders containing user's approved seeds)
        $stmt3 = mysqli_prepare($conn,
            'SELECT COUNT(DISTINCT o.id) AS cnt
             FROM orders o
             JOIN order_items oi ON oi.order_id = o.id
             JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
             WHERE sl.user_id = ? AND sl.status = "approved"'
        );
        mysqli_stmt_bind_param($stmt3, 'i', $userId);
        mysqli_stmt_execute($stmt3);
        $ordersReceivedCount = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt3))['cnt'] ?? 0);
        mysqli_stmt_close($stmt3);

        // Real stat: unread notifications count
        $stmt4 = mysqli_prepare($conn,
            'SELECT COUNT(*) AS cnt FROM order_notifications WHERE user_id = ? AND is_read = 0'
        );
        mysqli_stmt_bind_param($stmt4, 'i', $userId);
        mysqli_stmt_execute($stmt4);
        $notificationsCount = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt4))['cnt'] ?? 0);
        mysqli_stmt_close($stmt4);
        require_once __DIR__ . '/../Models/Seed.php';

        // Recommended seeds: up to 4 active, in-stock seeds
        $result = mysqli_query($conn,
            'SELECT i.id, i.name, i.category, i.price, i.planting_start_month, i.planting_end_month,
                    (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url
             FROM inventory i
             WHERE i.is_active = 1 AND i.stock_quantity > 0
             ORDER BY i.created_at DESC
             LIMIT 4'
        );
        $recommendedSeeds = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $startM = Seed::monthName($row['planting_start_month'] ?? null);
            $endM   = Seed::monthName($row['planting_end_month']   ?? null);
            $row['month_range'] = $startM && $endM ? "$startM – $endM" : ($startM ?: 'Year-round');
            $recommendedSeeds[] = $row;
        }

        // Planting schedule: current + next 2 months
        $currentMonth = (int)date('n');
        $months = [];
        for ($i = 0; $i < 3; $i++) {
            $m = (($currentMonth - 1 + $i) % 12) + 1;
            $months[] = $m;
        }
        $placeholders = implode(',', $months);
        $scheduleResult = mysqli_query($conn,
            "SELECT id, name, category, planting_start_month, planting_end_month
             FROM inventory
             WHERE is_active = 1
               AND planting_start_month IN ($placeholders)
             ORDER BY planting_start_month ASC
             LIMIT 9"
        );
        $scheduleSeeds = [];
        while ($row = mysqli_fetch_assoc($scheduleResult)) {
            $m = (int)$row['planting_start_month'];
            $row['month_abbr'] = strtoupper(substr(Seed::monthName($m), 0, 3));
            $row['is_now']     = ($m === $currentMonth);
            $scheduleSeeds[$m][] = $row;
        }

        require __DIR__ . '/../Views/dashboard.php';
    }

    public function profile(): void {
        $this->requireAuth();
        global $conn;

        $row = $this->userModel->findById($_SESSION['user_id']);
        if (!$row) { $this->sessionExpired(); }

        $user = [
            'first_name'    => $row['first_name'],
            'last_name'     => $row['last_name'],
            'username'      => $row['username'],
            'email'         => $row['email'],
            'phone_number'  => $row['phone_number'] ?? '',
            'address'       => $row['address']      ?? '',
            'profile_image' => $row['profile_image'] ?? '',
            'joined'        => date('F Y', strtotime($row['created_at'])),
        ];

        // Purchased orders
        $purchased = $this->orderModel->getByUser($_SESSION['user_id']);

        // Seed listings by this user
        $stmt = mysqli_prepare($conn,
            'SELECT i.id, i.name, i.category, i.price, i.stock_quantity, i.is_active,
                    sl.status AS listing_status, sl.created_at AS listed_at,
                    (SELECT image_url FROM seed_images WHERE inventory_id = i.id LIMIT 1) AS image_url
             FROM seed_listings sl
             JOIN inventory i ON i.id = sl.inventory_id
             WHERE sl.user_id = ?
             ORDER BY sl.created_at DESC'
        );
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $myListings = [];
        while ($r = mysqli_fetch_assoc($result)) {
            $myListings[] = $r;
        }
        mysqli_stmt_close($stmt);

        require __DIR__ . '/../Views/profile.php';
    }

    public function settings(): void {
        $this->requireAuth();
        $user    = $this->userModel->findById($_SESSION['user_id']);
        if (!$user) { $this->sessionExpired(); }

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'update_profile') {
                $first_name  = trim($_POST['first_name']   ?? '');
                $last_name   = trim($_POST['last_name']    ?? '');
                $username    = trim($_POST['username']     ?? '');
                $email       = trim($_POST['email']        ?? '');
                $phone       = trim($_POST['phone_number'] ?? '');
                $address     = trim($_POST['address']      ?? '');

                if (!$first_name || !$last_name || !$username || !$email) {
                    $error = 'Please fill in all required fields.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } elseif ($this->userModel->emailOrUsernameExists($email, $username, $_SESSION['user_id'])) {
                    $error = 'Email or username is already taken by another account.';
                } else {
                    // Handle profile image upload
                    if (!empty($_FILES['profile_image']['name'])) {
                        $uploadDir = __DIR__ . '/../../public/assets/uploads/profiles/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $ext     = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                        $allowed = ['jpg','jpeg','png','webp'];
                        if (!in_array($ext, $allowed)) {
                            $error = 'Profile image must be JPG, PNG, or WEBP.';
                        } elseif ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
                            $error = 'Profile image must be under 2MB.';
                        } else {
                            $filename = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                            move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $filename);
                            $this->userModel->updateProfileImage($_SESSION['user_id'], 'assets/uploads/profiles/' . $filename);
                            $user['profile_image'] = 'assets/uploads/profiles/' . $filename;
                        }
                    }

                    if (!$error) {
                        if ($this->userModel->updateProfile($_SESSION['user_id'], $first_name, $last_name, $username, $email, $phone, $address)) {
                            $_SESSION['first_name'] = $first_name;
                            $user = array_merge($user, [
                                'first_name'   => $first_name,
                                'last_name'    => $last_name,
                                'username'     => $username,
                                'email'        => $email,
                                'phone_number' => $phone,
                                'address'      => $address,
                            ]);
                            $success = 'Profile updated successfully.';
                        } else {
                            $error = 'Failed to update profile. Please try again.';
                        }
                    }
                }

            } elseif ($action === 'change_password') {
                $current = $_POST['current_password'] ?? '';
                $new     = $_POST['new_password']     ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                if (!$current || !$new || !$confirm) {
                    $error = 'Please fill in all password fields.';
                } elseif (strlen($new) < 6) {
                    $error = 'New password must be at least 6 characters.';
                } elseif ($new !== $confirm) {
                    $error = 'New passwords do not match.';
                } else {
                    $hash = $this->userModel->getPasswordHash($_SESSION['user_id']);
                    if (!password_verify($current, $hash)) {
                        $error = 'Current password is incorrect.';
                    } elseif ($this->userModel->updatePassword($_SESSION['user_id'], $new)) {
                        $success = 'Password changed successfully.';
                    } else {
                        $error = 'Failed to change password. Please try again.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/settings.php';
    }

    private function sessionExpired(): void {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
