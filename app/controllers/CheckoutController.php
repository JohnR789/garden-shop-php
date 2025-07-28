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
            $_SESSION['toast'] = [
                'message' => 'Your cart is empty!',
                'class' => 'bg-warning text-dark'
            ];
            header("Location: /cart");
            exit;
        }

        $error = '';
        $orderId = null;

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
                    unset($_SESSION['cart']);
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
