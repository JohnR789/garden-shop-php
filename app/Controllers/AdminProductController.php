<?php
namespace App\Controllers;

use App\Models\Product;
use App\Core\View;

/**
 * Admin controller for managing products.
 * Supports: add, edit, update, soft delete, bulk delete, undo (restore), and bulk restore.
 */
class AdminProductController
{
    /**
     * Show the admin product list and add form.
     * Shows deleted products if ?show_deleted=1 is set.
     */
    public function index()
    {
        $this->authorize();

        $showDeleted = !empty($_GET['show_deleted']);
        $products = Product::all($showDeleted);
        $categories = Product::categories(); 
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';
        View::render('admin/products', [
            'products' => $products,
            'categories' => $categories, 
            'error' => $error,
            'success' => $success,
            'showDeleted' => $showDeleted
        ]);
    }

    /**
     * Handle POST for adding a product.
     */
    public function add()
    {
        $this->authorize();
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = $_POST['category_id'] ?? null;

        // Validate required fields and image
        if (!$name || !$desc || !$price || !$category_id || empty($_FILES['image']['name'])) {
            header("Location: /admin/products?error=All fields required");
            exit;
        }

        // Validate and save image
        $img = $_FILES['image'];

        // === DEBUG: show PHP upload error code
        if ($img['error'] !== UPLOAD_ERR_OK) {
            header("Location: /admin/products?error=Upload failed: error code " . $img['error']);
            exit;
        }

        $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg'];
        if (!in_array($ext, $allowed)) {
            header("Location: /admin/products?error=Invalid image format");
            exit;
        }

        $basename = preg_replace('/[^a-z0-9]/i', '_', strtolower($name));
        $newName = $basename . '_' . time() . '.' . $ext;
        $dest = __DIR__ . '/../../public/assets/images/' . $newName;

        // === DEBUG: confirm temp file exists and show target path
        if (!file_exists($img['tmp_name'])) {
            header("Location: /admin/products?error=Upload failed: temp file missing ($img[tmp_name])");
            exit;
        }

        if (!move_uploaded_file($img['tmp_name'], $dest)) {
            header("Location: /admin/products?error=Upload failed: could not move file (from $img[tmp_name] to $dest)");
            exit;
        }

        $imagePath = '/assets/images/' . $newName;
        Product::create($name, $desc, $price, $imagePath, $category_id);
        header("Location: /admin/products?success=Product+added");
        exit;
    }

    /**
     * Show edit form for a product.
     */
    public function edit()
    {
        $this->authorize();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: /admin/products?error=Product+ID+missing");
            exit;
        }
        $product = Product::find($id);
        if (!$product) {
            header("Location: /admin/products?error=Product+not+found");
            exit;
        }
        $categories = Product::categories();
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';
        View::render('admin/edit_product', [
            'product' => $product,
            'categories' => $categories, 
            'error' => $error,
            'success' => $success
        ]);
    }

    /**
     * Handle product update POST.
     */
    public function update()
    {
        $this->authorize();
        $id = $_POST['id'] ?? null;
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = $_POST['category_id'] ?? null;

        if (!$id || !$name || !$desc || !$price || !$category_id) {
            header("Location: /admin/products/edit?id=$id&error=All+fields+required");
            exit;
        }

        $product = Product::find($id);
        if (!$product) {
            header("Location: /admin/products?error=Product+not+found");
            exit;
        }

        // Handle image upload (optional)
        $imagePath = $product['image'];
        if (!empty($_FILES['image']['name'])) {
            $img = $_FILES['image'];

            // === DEBUG: show PHP upload error code
            if ($img['error'] !== UPLOAD_ERR_OK) {
                header("Location: /admin/products/edit?id=$id&error=Upload failed: error code " . $img['error']);
                exit;
            }

            $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
            $allowed = ['png', 'jpg', 'jpeg'];
            if (!in_array($ext, $allowed)) {
                header("Location: /admin/products/edit?id=$id&error=Invalid image format");
                exit;
            }
            $basename = preg_replace('/[^a-z0-9]/i', '_', strtolower($name));
            $newName = $basename . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../../public/assets/images/' . $newName;

            // === DEBUG: confirm temp file exists and show target path
            if (!file_exists($img['tmp_name'])) {
                header("Location: /admin/products/edit?id=$id&error=Upload failed: temp file missing ($img[tmp_name])");
                exit;
            }

            if (!move_uploaded_file($img['tmp_name'], $dest)) {
                header("Location: /admin/products/edit?id=$id&error=Upload failed: could not move file (from $img[tmp_name] to $dest)");
                exit;
            }
            $imagePath = '/assets/images/' . $newName;
        }

        Product::update($id, $name, $desc, $price, $imagePath, $category_id);
        header("Location: /admin/products?success=Product+updated");
        exit;
    }

    /**
     * Soft delete a product (single).
     */
    public function delete()
    {
        $this->authorize();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header("Location: /admin/products?error=Product+ID+missing");
            exit;
        }
        if (!Product::find($id)) {
            header("Location: /admin/products?error=Product+not+found");
            exit;
        }
        Product::delete($id);
        header("Location: /admin/products?success=Product+deleted");
        exit;
    }

    /**
     * Bulk delete products (array of IDs).
     */
    public function bulkDelete()
    {
        $this->authorize();
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            header("Location: /admin/products?error=No+products+selected");
            exit;
        }
        Product::bulkDelete($ids);
        header("Location: /admin/products?success=Products+deleted");
        exit;
    }

    /**
     * Restore (undo) a soft-deleted product by ID.
     */
    public function restore()
    {
        $this->authorize();
        $id = $_POST['id'] ?? null;
        if ($id) {
            Product::restore($id);
            header("Location: /admin/products?success=Product+restored");
        } else {
            header("Location: /admin/products?error=Product+ID+missing");
        }
        exit;
    }

    /**
     * Bulk restore soft-deleted products (array of IDs).
     */
    public function bulkRestore()
    {
        $this->authorize();
        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids) || empty($ids)) {
            header("Location: /admin/products?error=No+products+selected");
            exit;
        }
        Product::bulkRestore($ids);
        header("Location: /admin/products?success=Products+restored");
        exit;
    }

    /**
     * Restricts all admin routes to logged-in admin users only.
     */
    private function authorize()
    {
        if (empty($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }
        // Check is_admin (fetch from DB)
        $user = \App\Models\User::findByUsername($_SESSION['user']);
        if (empty($user['is_admin'])) {
            http_response_code(403);
            echo "Forbidden: Admins only.";
            exit;
        }
    }
}
