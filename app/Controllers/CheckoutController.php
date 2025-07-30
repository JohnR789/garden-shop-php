<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Core\View;

/**
 * Checkout process controller.
 */
class CheckoutController {
    /**
     * Show checkout form or handle submission.
     */
    public function index() {
        $products = [];
        $total = 0;
        $error = '';
        $orderId = null;
        $name = '';
        $email = '';
        $address = '';

        // Get cart items: DB for logged-in, session for guest
        if (!empty($_SESSION['user_id'])) {
            // Logged in: fetch DB-backed cart
            $cart = Cart::getOrCreateActiveCart($_SESSION['user_id']);
            $items = Cart::getItems($cart['id']);
            foreach ($items as $item) {
                $item['qty'] = $item['quantity'];
                $item['subtotal'] = $item['qty'] * $item['price'];
                $item['id'] = $item['product_id']; // Standardize for Order model!
                $products[] = $item;
                $total += $item['subtotal'];
            }
        } else {
            // Guest: fetch from session
            $cart = $_SESSION['cart'] ?? [];
            foreach ($cart as $id => $qty) {
                $product = Product::find($id);
                if ($product) {
                    $product['qty'] = $qty;
                    $product['subtotal'] = $qty * $product['price'];
                    $products[] = $product;
                    $total += $product['subtotal'];
                }
            }
        }

        // If cart is empty, redirect back to cart page
        if (!$products) {
            $_SESSION['toast'] = [
                'message' => 'Your cart is empty!',
                'class' => 'bg-warning text-dark'
            ];
            header("Location: /cart");
            exit;
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (!$name || !$email || !$address) {
                $_SESSION['toast'] = [
                    'message' => 'Please fill in all fields.',
                    'class' => 'bg-danger'
                ];
                $error = "Please fill in all required fields.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['toast'] = [
                    'message' => 'Invalid email address.',
                    'class' => 'bg-danger'
                ];
                $error = "Invalid email address.";
            } else {
                try {
                    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                    $orderId = Order::create(
                        ['name' => $name, 'email' => $email, 'address' => $address],
                        $products,
                        $total,
                        $user_id
                    );

                    // After order: clear the correct cart
                    if (!empty($_SESSION['user_id'])) {
                        // DB: clear cart in DB for user
                        Cart::clear($cart['id']);
                    } else {
                        // Guest: clear cart in session
                        unset($_SESSION['cart']);
                    }

                    $_SESSION['toast'] = [
                        'message' => 'Order placed successfully!',
                        'class' => 'bg-success'
                    ];
                } catch (\Exception $e) {
                    $error = "Order failed: " . $e->getMessage();
                    $_SESSION['toast'] = [
                        'message' => $error,
                        'class' => 'bg-danger'
                    ];
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

