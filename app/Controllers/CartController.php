<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Cart.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Seed.php';

class CartController {

    private Cart $cartModel;
    private User $userModel;
    private Seed $seedModel;

    public function __construct() {
        global $conn;
        $this->cartModel = new Cart($conn);
        $this->userModel = new User($conn);
        $this->seedModel = new Seed($conn);
    }

    private function requireAuth(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    /** Show the cart page */
    public function index(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $user      = $this->userModel->findById($_SESSION['user_id']);
        $cartItems = $this->cartModel->getByUser($_SESSION['user_id']);

        // Calculate total
        $total = array_reduce($cartItems, fn($carry, $item) =>
            $carry + ($item['price'] * $item['quantity']), 0
        );

        require __DIR__ . '/../Views/cart.php';
    }

    /** Add item to cart (POST from marketplace or seed details) */
    public function add(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $inventoryId = (int)($_POST['seed_id'] ?? 0);
        $quantity    = max(1, (int)($_POST['quantity'] ?? 1));

        if ($inventoryId > 0) {
            $seed = $this->seedModel->findInStock($inventoryId);
            if ($seed) {
                // Block user from buying their own seed
                if ($this->seedModel->isOwnedBy($inventoryId, $_SESSION['user_id'])) {
                    header('Location: marketplace.php?error=own_seed');
                    exit;
                }
                $this->cartModel->addItem($_SESSION['user_id'], $inventoryId, $quantity);
            }
        }

        header('Location: cart.php');
        exit;
    }

    /** Update quantity of a cart item (POST from cart page) */
    public function update(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $cartId   = (int)($_POST['cart_id']  ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($cartId > 0) {
            $this->cartModel->updateQuantity($cartId, $_SESSION['user_id'], $quantity);
        }

        // Return JSON for AJAX or redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            $cartItems = $this->cartModel->getByUser($_SESSION['user_id']);
            $total     = array_reduce($cartItems, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0);
            echo json_encode(['success' => true, 'total' => $total]);
            exit;
        }

        header('Location: cart.php');
        exit;
    }

    /** Remove an item from cart (POST from cart page) */
    public function remove(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $this->requireAuth();

        $cartId = (int)($_POST['cart_id'] ?? 0);
        if ($cartId > 0) {
            $this->cartModel->removeItem($cartId, $_SESSION['user_id']);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            $cartItems = $this->cartModel->getByUser($_SESSION['user_id']);
            $total     = array_reduce($cartItems, fn($c, $i) => $c + ($i['price'] * $i['quantity']), 0);
            echo json_encode(['success' => true, 'total' => $total, 'empty' => empty($cartItems)]);
            exit;
        }

        header('Location: cart.php');
        exit;
    }
}
