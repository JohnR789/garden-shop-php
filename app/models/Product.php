<?php
namespace App\Models;

use PDO;

/**
 * Handles product data access and management, including category, search, sorting, and pagination.
 */
class Product {
    /**
     * Fetch all products from database, newest first.
     * Optionally filter by category.
     * By default, excludes soft-deleted products unless $withDeleted is true.
     * Supports sorting: newest, price_asc, price_desc, name_asc, name_desc.
     */
    public static function all($withDeleted = false, $category_id = null, $sort = 'newest') {
        $pdo = self::pdo();
        $sql = 'SELECT * FROM products';
        $params = [];

        $conditions = [];
        if (!$withDeleted) {
            $conditions[] = 'deleted_at IS NULL';
        }
        if ($category_id !== null && $category_id !== '') {
            $conditions[] = 'category_id = ?';
            $params[] = $category_id;
        }
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ' . self::sortSql($sort);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all products in a single category, with optional sorting.
     * Excludes deleted products by default.
     */
    public static function allByCategory($category_id, $withDeleted = false, $sort = 'newest') {
        return self::all($withDeleted, $category_id, $sort);
    }

    /**
     * Search products by keyword (in name or description), with sorting.
     * Excludes deleted products by default.
     * Optional category filtering.
     */
    public static function search($query, $category_id = null, $sort = 'newest') {
        $pdo = self::pdo();
        $sql = 'SELECT * FROM products WHERE deleted_at IS NULL AND (name ILIKE ? OR description ILIKE ?)';
        $params = ['%' . $query . '%', '%' . $query . '%'];

        if ($category_id) {
            $sql .= ' AND category_id = ?';
            $params[] = $category_id;
        }

        $sql .= ' ' . self::sortSql($sort);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Converts sort key to SQL.
     */
    protected static function sortSql($sort) {
        switch ($sort) {
            case 'price_asc': return 'ORDER BY price ASC';
            case 'price_desc': return 'ORDER BY price DESC';
            case 'name_asc': return 'ORDER BY name ASC';
            case 'name_desc': return 'ORDER BY name DESC';
            default: return 'ORDER BY id DESC'; // newest
        }
    }

    /**
     * Pagination: returns ['data' => [...], 'total' => n]
     * Uses LIMIT/OFFSET directly (Postgres-safe).
     */
    public static function paginated($withDeleted = false, $category_id = null, $sort = 'newest', $search = '', $page = 1, $perPage = 9) {
        $pdo = self::pdo();
        $sql = 'SELECT * FROM products';
        $params = [];
        $conditions = [];
        if (!$withDeleted) $conditions[] = 'deleted_at IS NULL';
        if ($category_id !== null && $category_id !== '') {
            $conditions[] = 'category_id = ?';
            $params[] = $category_id;
        }
        if ($search !== '') {
            $conditions[] = '(name ILIKE ? OR description ILIKE ?)';
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($conditions) $sql .= ' WHERE ' . implode(' AND ', $conditions);
        $sql .= ' ' . self::sortSql($sort);
        // Use integer casting for LIMIT/OFFSET, not parameters (prevents SQL syntax error)
        $offset = max(0, ((int)$page - 1) * (int)$perPage);
        $limit = (int)$perPage;
        $sql .= " LIMIT $limit OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM products" . 
            ($conditions ? ' WHERE ' . implode(' AND ', $conditions) : ''));
        $total = $totalStmt->fetchColumn();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * Find a product by its ID.
     */
    public static function find($id) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new product to the database, including category.
     */
    public static function create($name, $description, $price, $image, $category_id = null) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$name, $description, $price, $image, $category_id]);
    }

    /**
     * Update an existing product (including category).
     */
    public static function update($id, $name, $description, $price, $image, $category_id = null) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('UPDATE products SET name = ?, description = ?, price = ?, image = ?, category_id = ? WHERE id = ?');
        return $stmt->execute([$name, $description, $price, $image, $category_id, $id]);
    }

    /**
     * Soft delete a product by ID (sets deleted_at for undo support).
     */
    public static function delete($id) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('UPDATE products SET deleted_at = NOW() WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Bulk soft delete (array of IDs).
     */
    public static function bulkDelete($ids) {
        if (empty($ids)) return false;
        $pdo = self::pdo();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE products SET deleted_at = NOW() WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    /**
     * Restore a soft-deleted product by ID.
     */
    public static function restore($id) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('UPDATE products SET deleted_at = NULL WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Bulk restore soft-deleted products (array of IDs).
     */
    public static function bulkRestore($ids) {
        if (empty($ids)) return false;
        $pdo = self::pdo();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE products SET deleted_at = NULL WHERE id IN ($placeholders)");
        return $stmt->execute($ids);
    }

    /**
     * Fetch all product categories.
     */
    public static function categories() {
        $pdo = self::pdo();
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a category by its ID.
     */
    public static function findCategory($id) {
        $pdo = self::pdo();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Return PDO connection, using config from .env.
     */
    protected static function pdo() {
        $cfg = require __DIR__ . '/../../config/config.php';
        return new PDO(
            "pgsql:host={$cfg['db_host']};dbname={$cfg['db_name']};port=" . ($cfg['db_port'] ?? 5432),
            $cfg['db_user'], $cfg['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}





