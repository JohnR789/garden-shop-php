<?php
namespace App\Models;

use PDO;

class Cart
{
    // Fetch the user's active cart (creates one if not exists)
    public static function getOrCreateActiveCart($user_id)
    {
        $pdo = self::pdo();
        // Try to find an active cart
        $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) return $cart;

        // If not found, create one
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, status) VALUES (?, 'active') RETURNING id");
        $stmt->execute([$user_id]);
        $cart_id = $stmt->fetchColumn();

        // Return the new cart
        return [
            'id' => $cart_id,
            'user_id' => $user_id,
            'status' => 'active'
        ];
    }

    // Add or update a product in the cart
    public static function addItem($cart_id, $product_id, $qty = 1)
    {
        $pdo = self::pdo();
        // Check if already exists
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
        $stmt->execute([$cart_id, $product_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Update quantity
            $newQty = $item['quantity'] + $qty;
            $update = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $update->execute([$newQty, $item['id']]);
        } else {
            // Add new item
            $insert = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$cart_id, $product_id, $qty]);
        }
    }

    // Update the quantity for a cart item
    public static function updateItem($cart_id, $product_id, $qty)
    {
        $pdo = self::pdo();
        if ($qty > 0) {
            $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$qty, $cart_id, $product_id]);
        } else {
            // Remove if quantity <= 0
            self::removeItem($cart_id, $product_id);
        }
    }

    // Remove an item from cart
    public static function removeItem($cart_id, $product_id)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?");
        $stmt->execute([$cart_id, $product_id]);
    }

    // Get all items in the cart (with product info)
    public static function getItems($cart_id)
    {
        $pdo = self::pdo();
        $sql = "SELECT ci.*, p.name, p.price, p.image, p.description
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.cart_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cart_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count total items in cart (sum of quantities)
    public static function countItems($cart_id)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
        return (int) $stmt->fetchColumn();
    }

    // Get the user's active cart_id (or null)
    public static function getActiveCartId($user_id)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare("SELECT id FROM carts WHERE user_id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    // Delete all items in a cart
    public static function clear($cart_id)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cart_id]);
    }

    // Mark cart as 'ordered' (called after successful checkout)
    public static function markOrdered($cart_id)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare("UPDATE carts SET status = 'ordered' WHERE id = ?");
        $stmt->execute([$cart_id]);
    }

    // PDO helper
    protected static function pdo()
    {
        $cfg = require __DIR__ . '/../../config/config.php';
        return new PDO(
            "pgsql:host={$cfg['db_host']};dbname={$cfg['db_name']};port=" . ($cfg['db_port'] ?? 5432),
            $cfg['db_user'], $cfg['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}

