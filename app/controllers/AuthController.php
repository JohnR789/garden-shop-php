<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Core\View;

/**
 * Handles user login, logout, and registration.
 */
class AuthController {
    /**
     * Login form and handler.
     */
    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = User::findByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                // Set session user & user_id
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];

                // Merge session cart (if exists) into DB cart
                if (!empty($_SESSION['cart'])) {
                    $cart = Cart::getOrCreateActiveCart($user['id']);
                    foreach ($_SESSION['cart'] as $pid => $qty) {
                        Cart::addItem($cart['id'], $pid, $qty);
                    }
                    unset($_SESSION['cart']);
                }

                $_SESSION['toast'] = [
                    'message' => 'Welcome back!',
                    'class' => 'bg-success'
                ];
                header("Location: /");
                exit;
            } else {
                $_SESSION['toast'] = [
                    'message' => 'Invalid login.',
                    'class' => 'bg-danger'
                ];
                $error = "Invalid credentials";
            }
        }
        View::render('auth/login', ['error' => $error]);
    }

    /**
     * User registration form and handler.
     */
    public function register() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';
            if (!$username || !$password) {
                $_SESSION['toast'] = [
                    'message' => 'Please fill in all fields.',
                    'class' => 'bg-danger'
                ];
                $error = "Username and password required";
            } elseif ($password !== $confirm) {
                $_SESSION['toast'] = [
                    'message' => 'Passwords do not match.',
                    'class' => 'bg-danger'
                ];
                $error = "Passwords do not match";
            } elseif (User::findByUsername($username)) {
                $_SESSION['toast'] = [
                    'message' => 'Username already exists.',
                    'class' => 'bg-danger'
                ];
                $error = "Username already exists";
            } else {
                User::create($username, $password);
                $_SESSION['toast'] = [
                    'message' => 'Registration successful! Please login.',
                    'class' => 'bg-success'
                ];
                header("Location: /login");
                exit;
            }
        }
        View::render('auth/register', ['error' => $error]);
    }

    /**
     * Log user out.
     */
    public function logout() {
        unset($_SESSION['user'], $_SESSION['user_id']);
        unset($_SESSION['cart']); // Ensure no leftover guest cart on new login
        $_SESSION['toast'] = [
            'message' => 'Logged out successfully.',
            'class' => 'bg-success'
        ];
        header("Location: /");
        exit;
    }
}
