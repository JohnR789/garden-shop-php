<?php
namespace App\Controllers;

use App\Models\Product;
use App\Core\View;

/**
 * Cart logic using PHP sessions.
 */
class CartController {
    /**
     * View cart.
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
        View::render('cart/index', ['products' => $products, 'total' => $total]);
    }

    /**
     * Add an item to the cart (supports AJAX toast and returns new cart count).
     */
    public function add() {
        $id = $_POST['id'] ?? null;
        $product = $id ? Product::find($id) : null;
        if (!$product) {
            $cart_count = array_sum($_SESSION['cart'] ?? []);
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found.',
                    'cart_count' => $cart_count
                ]);
                exit;
            }
            $_SESSION['toast'] = [
                'message' => 'Product not found.',
                'class' => 'bg-danger'
            ];
            header("Location: /");
            exit;
        }
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
        $_SESSION['cart'][$id] += 1;

        $cart_count = array_sum($_SESSION['cart']);
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => "{$product['name']} added to cart!",
                'cart_count' => $cart_count
            ]);
            exit;
        }
        $_SESSION['toast'] = [
            'message' => "{$product['name']} added to cart!",
            'class' => 'bg-success'
        ];
        header("Location: /cart");
        exit;
    }

    /**
     * Remove an item from cart.
     */
    public function remove() {
        $id = $_POST['id'] ?? null;
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            $_SESSION['toast'] = [
                'message' => 'Product removed from cart.',
                'class' => 'bg-warning text-dark'
            ];
        } else {
            $_SESSION['toast'] = [
                'message' => 'Product not found in cart.',
                'class' => 'bg-danger'
            ];
        }
        header("Location: /cart");
        exit;
    }

    private function isAjax() {
        return (
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }
}


