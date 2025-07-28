<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Core\View;

/**
 * Checkout process controller.
 */
class CheckoutController {
    /**
     * Show checkout form or handle submission.
     */
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $products = [];
        $total = 0;

        foreach ($cart as $id => $qty) {
            $product = Product::find($id);
            if ($product) {
                $product['qty'] = $qty;
                $product['subtotal'] = $qty * $product['price'];
                $products[] = $product;
                $total += $product['subtotal'];
            }
        }

        // If cart empty, redirect
        if (!$products) {
            header("Location: /cart");
            exit;
        }

        $error = '';
        $orderId = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            // Basic validation (expand as needed)
            if (!$name || !$email || !$address) {
                $error = "Please fill in all required fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email address.";
            } else {
                try {
                    // Get user ID if logged in (null otherwise)
                    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                    // For debugging: log session data (remove or comment out in production!)
                    // error_log("SESSION: " . print_r($_SESSION, true));
                    
                    $orderId = Order::create(
                        ['name' => $name, 'email' => $email, 'address' => $address],
                        $products,
                        $total,
                        $user_id
                    );
                    // Clear the cart
                    unset($_SESSION['cart']);
                } catch (\Exception $e) {
                    // Show actual error for debugging (remove $e->getMessage() in production)
                    $error = "Order failed: " . $e->getMessage();
                }
            }
        }

        // Render confirmation if order placed, else show form
        if ($orderId) {
            View::render('checkout/success', ['orderId' => $orderId, 'customer_name' => $name]);
        } else {
            View::render('checkout/index', [
                'products' => $products,
                'total' => $total,
                'error' => $error,
                'form' => $_POST
            ]);
        }
    }
}
