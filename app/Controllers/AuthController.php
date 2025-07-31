<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Core\View;

/**
 * Handles user login, logout, and registration.
 */
class AuthController
{
    /**
     * Login form and handler.
     */
    public function login()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Allow login by username OR email
            $user = User::findByUsernameOrEmail($input);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = !empty($user['is_admin']); 

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
    public function register()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm'] ?? '';
            $email    = trim($_POST['email'] ?? '');
            $name     = trim($_POST['name'] ?? '');

            if (!$username || !$password || !$email || !$name) {
                $_SESSION['toast'] = [
                    'message' => 'Please fill in all fields.',
                    'class' => 'bg-danger'
                ];
                $error = "All fields are required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['toast'] = [
                    'message' => 'Invalid email address.',
                    'class' => 'bg-danger'
                ];
                $error = "Invalid email address";
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
            } elseif (User::findByEmail($email)) {
                $_SESSION['toast'] = [
                    'message' => 'Email already registered.',
                    'class' => 'bg-danger'
                ];
                $error = "Email already registered";
            } else {
                User::create($username, $password, $email, $name);
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
    public function logout()
    {
        unset($_SESSION['user'], $_SESSION['user_id'], $_SESSION['is_admin']); 
        unset($_SESSION['cart']);
        $_SESSION['toast'] = [
            'message' => 'Logged out successfully.',
            'class' => 'bg-success'
        ];
        header("Location: /");
        exit;
    }
}


