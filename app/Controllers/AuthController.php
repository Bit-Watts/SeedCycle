<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController {

    private User $user;

    public function __construct() {
        global $conn;
        $this->user = new User($conn);
    }

    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password =      $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                $error = 'Please fill in all fields.';
            } else {
                $row = $this->user->findByEmail($email);

                if ($row && password_verify($password, $row['password_hash'])) {
                    if (isset($row['is_active']) && (int)$row['is_active'] === 0) {
                        $error = 'Your account has been deactivated. Please contact support.';
                    } else {
                        $_SESSION['user_id']       = $row['id'];
                        $_SESSION['first_name']    = $row['first_name'];
                        $_SESSION['last_name']     = $row['last_name'];
                        $_SESSION['username']      = $row['username'];
                        $_SESSION['email']         = $row['email'];
                        $_SESSION['role']          = $row['role'] ?? 'user';
                        $_SESSION['profile_image'] = $row['profile_image'] ?? '';

                        if (($row['role'] ?? 'user') === 'admin') {
                            header('Location: admin/dashboard.php');
                        } else {
                            header('Location: index.php');
                        }
                        exit;
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function signup(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $first_name       = trim($_POST['first_name']       ?? '');
            $last_name        = trim($_POST['last_name']        ?? '');
            $email            = trim($_POST['email']            ?? '');
            $username         = trim($_POST['username']         ?? '');
            $password         =      $_POST['password']         ?? '';
            $confirm_password =      $_POST['confirm_password'] ?? '';
            $phone_number     = trim($_POST['phone_number']     ?? '');
            $address          = trim($_POST['address']          ?? '');

            if (!$first_name || !$last_name || !$email || !$username || !$password || !$confirm_password) {
                $error = 'Please fill in all fields.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } elseif ($this->user->emailOrUsernameExists($email, $username)) {
                $error = 'Email or username is already taken.';
            } else {
                // Handle profile image upload
                $profileImage = '';
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
                        $filename     = 'profile_' . time() . '_' . uniqid() . '.' . $ext;
                        move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir . $filename);
                        $profileImage = 'assets/uploads/profiles/' . $filename;
                    }
                }

                if (!$error) {
                    if ($this->user->createFull($first_name, $last_name, $email, $username, $password, $phone_number, $address, $profileImage)) {
                        $success = 'Account created! <a href="login.php">Login here</a>.';
                    } else {
                        $error = 'Something went wrong. Please try again.';
                    }
                }
            }
        }

        require __DIR__ . '/../Views/auth/signup.php';
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
