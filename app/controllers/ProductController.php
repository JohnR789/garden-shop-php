<?php
namespace App\Controllers;

use App\Models\Product;
use App\Core\View;

/**
 * Controller for product browsing and details.
 */
class ProductController {
    /**
     * Show the main product grid with category filtering, search, sorting, and pagination.
     */
    public function index() {
        // Get all categories for filter dropdown
        $categories = Product::categories();

        // Get current filter/sort/search/page values
        $selected_category = $_GET['category'] ?? null;
        $search_query = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'newest';
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = 9;

        // Use paginated fetch
        $result = Product::paginated(false, $selected_category, $sort, $search_query, $page, $perPage);
        $products = $result['data'];
        $total = $result['total'];
        $totalPages = ceil($total / $perPage);

        // Pass everything to the view
        View::render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'selected_category' => $selected_category,
            'search_query' => $search_query,
            'sort' => $sort,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * Show a single product detail page.
     */
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: /");
            exit;
        }
        $product = Product::find($id);
        if (!$product) {
            http_response_code(404);
            echo "Product not found.";
            exit;
        }
        View::render('products/show', ['product' => $product]);
    }
}



