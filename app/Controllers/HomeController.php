<?php

class HomeController {

    public function landing(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        // Redirect logged-in users to dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        require __DIR__ . '/../Views/home.php';
    }
}
