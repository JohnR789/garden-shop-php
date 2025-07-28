<?php
namespace App\Controllers;

use App\Models\User;
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
                $_SESSION['user'] = $user['username'];
                header("Location: /");
                exit;
            } else {
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
                $error = "Username and password required";
            } elseif ($password !== $confirm) {
                $error = "Passwords do not match";
            } elseif (User::findByUsername($username)) {
                $error = "Username already exists";
            } else {
                User::create($username, $password);
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
        unset($_SESSION['user']);
        header("Location: /");
        exit;
    }
}
