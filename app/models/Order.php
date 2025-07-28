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
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, name, email, address, total) VALUES (?, ?, ?, ?, ?) RETURNING id');
            $stmt->execute([
                $user_id,
                $customer['name'],
                $customer['email'],
                $customer['address'],
                $total
            ]);
            $order_id = $stmt->fetchColumn();

            // Insert each cart item into order_items
            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
            foreach ($cart as $item) {
                $itemStmt->execute([
                    $order_id,
                    $item['id'],
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
            "pgsql:host={$cfg['db_host']};dbname={$cfg['db_name']};port=" . ($cfg['db_port'] ?? 5432),
            $cfg['db_user'], $cfg['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}
