<?php
// public/api/search-products.php
// This file handles the search functionality for products.
require_once __DIR__ . '/../../app/models/Product.php';

use App\Models\Product;

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');
$results = [];

if ($query !== '') {
    // Limit to 8 results for speed
    $products = Product::search($query);
    foreach (array_slice($products, 0, 8) as $product) {
        $results[] = [
            'id'    => $product['id'],
            'name'  => $product['name'],
            'image' => $product['image'],
            'price' => $product['price']
        ];
    }
}

echo json_encode($results);

