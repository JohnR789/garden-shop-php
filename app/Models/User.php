<?php
namespace App\Models;

use PDO;

class User
{
    /**
     * Find user by username.
     */
    public static function findByUsername($username)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find user by email.
     */
    public static function findByEmail($email)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find user by username OR email (for login).
     */
    public static function findByUsernameOrEmail($input)
    {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$input, $input]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Register a new user.
     */
    public static function create($username, $password, $email, $name)
    {
        $pdo = self::pdo();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password, is_admin, username) VALUES (?, ?, ?, false, ?)'
        );
        return $stmt->execute([$name, $email, $hash, $username]);
    }

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
