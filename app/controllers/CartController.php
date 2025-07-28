<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Core\View;

/**
 * Cart logic, using DB cart for logged-in, session for guest.
 */
class CartController {
    /**
     * View cart.
     */
    public function index() {
        $products = [];
        $total = 0;
        if (!empty($_SESSION['user_id'])) {
            // DB-backed cart
            $cart = Cart::getOrCreateActiveCart($_SESSION['user_id']);
            $items = Cart::getItems($cart['id']);
            foreach ($items as $item) {
                $item['subtotal'] = $item['price'] * $item['quantity'];
                $products[] = [
                    'id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'qty' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ];
                $total += $item['subtotal'];
            }
        } else {
            // Guest session cart
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
        View::render('cart/index', ['products' => $products, 'total' => $total]);
    }

    /**
     * Add an item to the cart (AJAX toast, badge support, DB for user).
     */
    public function add() {
        $id = $_POST['id'] ?? null;
        $product = $id ? Product::find($id) : null;
        $cart_count = 0;
        $msg = 'Product not found.';
        $cls = 'bg-danger';

        if (!empty($_SESSION['user_id'])) {
            // DB cart for logged-in users
            $user_id = $_SESSION['user_id'];
            $cart = Cart::getOrCreateActiveCart($user_id);
            if ($product) {
                Cart::addItem($cart['id'], $id, 1);
                $cart_count = Cart::countItems($cart['id']);
                $msg = "{$product['name']} added to cart!";
                $cls = 'bg-success';
            } else {
                $cart_count = Cart::countItems($cart['id']);
            }
        } else {
            // Guest session cart
            if ($product) {
                if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
                if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
                $_SESSION['cart'][$id] += 1;
                $cart_count = array_sum($_SESSION['cart']);
                $msg = "{$product['name']} added to cart!";
                $cls = 'bg-success';
            } else {
                $cart_count = array_sum($_SESSION['cart'] ?? []);
            }
        }

        // AJAX or redirect
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $product ? true : false,
                'message' => $msg,
                'cart_count' => $cart_count
            ]);
            exit;
        }
        $_SESSION['toast'] = [
            'message' => $msg,
            'class' => $cls
        ];
        header("Location: /cart");
        exit;
    }

    /**
     * Remove an item from cart (DB or session).
     */
    public function remove() {
        $id = $_POST['id'] ?? null;
        $msg = 'Product not found in cart.';
        $cls = 'bg-danger';
        if (!empty($_SESSION['user_id'])) {
            $cart = Cart::getOrCreateActiveCart($_SESSION['user_id']);
            $items = Cart::getItems($cart['id']);
            $found = false;
            foreach ($items as $item) {
                if ($item['product_id'] == $id) $found = true;
            }
            if ($found) {
                Cart::removeItem($cart['id'], $id);
                $msg = 'Product removed from cart.';
                $cls = 'bg-warning text-dark';
            }
        } else {
            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
                $msg = 'Product removed from cart.';
                $cls = 'bg-warning text-dark';
            }
        }
        $_SESSION['toast'] = [
            'message' => $msg,
            'class' => $cls
        ];
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

