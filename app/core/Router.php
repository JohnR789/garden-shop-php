<?php
namespace App\Core;

/**
 * Simple router that maps URLs to controller actions.
 * Extend here for more routes.
 */
class Router {
    public function dispatch($uri) {
        $parsed = parse_url($uri);
        $path = $parsed['path'] ?? '/';

        // Route table. Add more as needed.
        $routes = [
            '/' => ['ProductController', 'index'],
            '/product' => ['ProductController', 'show'],
            '/cart' => ['CartController', 'index'],
            '/cart/add' => ['CartController', 'add'],
            '/cart/remove' => ['CartController', 'remove'],
            '/login' => ['AuthController', 'login'],
            '/logout' => ['AuthController', 'logout'],
            '/register' => ['AuthController', 'register'],
            '/checkout' => ['CheckoutController', 'index'],
            '/admin/products' => ['AdminProductController', 'index'],
            '/admin/products/add' => ['AdminProductController', 'add'],
            '/admin/products/edit' => ['AdminProductController', 'edit'],
            '/admin/products/update' => ['AdminProductController', 'update'],
            '/admin/products/delete'       => ['AdminProductController', 'delete'],
            '/admin/products/bulk-delete'  => ['AdminProductController', 'bulkDelete'],
            '/admin/products/restore'      => ['AdminProductController', 'restore'],
            '/admin/products/bulk-restore' => ['AdminProductController', 'bulkRestore'],
            '/about' => ['PageController', 'about'],
            '/contact' => ['PageController', 'contact'],
        ];

        // Route match
        if (isset($routes[$path])) {
            [$controller, $action] = $routes[$path];
            $controllerClass = "\\App\\Controllers\\$controller";
            if (class_exists($controllerClass)) {
                $ctrl = new $controllerClass();
                $ctrl->$action();
                return;
            }
        }
        // If no route found, render 404
        http_response_code(404);
        echo "Page not found";
    }
}
