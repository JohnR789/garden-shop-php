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
        // For each cart item, fetch details and calc total
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
     * Add an item to the cart.
     */
    public function add() {
        $id = $_POST['id'] ?? null;
        if (!$id || !Product::find($id)) {
            header("Location: /"); exit;
        }
        // If not set, initialize cart
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
        $_SESSION['cart'][$id] += 1;
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
        }
        header("Location: /cart");
        exit;
    }
}
