<?php
namespace App\Models;

use PDO;

class Order {
    public static function create($customer, $cart, $total, $user_id = null) {
        $pdo = self::pdo();

        // Start a transaction for safety
        $pdo->beginTransaction();

        try {
            // Insert order (user_id can be null for guest orders)
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, customer_name, customer_email, customer_address, total) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $user_id,
                $customer['name'],
                $customer['email'],
                $customer['address'],
                $total
            ]);
            $order_id = $pdo->lastInsertId();

            // Insert each cart item into order_items
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)');
            foreach ($cart as $item) {
                $itemStmt->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['qty'],
                    $item['price']
                ]);
            }

            $pdo->commit();
            return $order_id;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    protected static function pdo() {
        $cfg = require __DIR__ . '/../../config/config.php';
        return new PDO(
            "mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset=utf8mb4",
            $cfg['db_user'], $cfg['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
