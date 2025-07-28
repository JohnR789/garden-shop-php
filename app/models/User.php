<?php
namespace App\Models;

use PDO;

class User {
    /**
     * Find user by username (for login).
     */
    public static function findByUsername($username) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Register a new user.
     */
    public static function create($username, $password) {
        $pdo = self::pdo();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        return $stmt->execute([$username, $hash]);
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
