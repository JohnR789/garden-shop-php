<?php
namespace App\Core;

/**
 * Basic view renderer with header/footer layout.
 */
class View {
    public static function render($view, $data = []) {
        extract($data);
        require __DIR__ . "/../views/layout/header.php";
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . "/../views/layout/footer.php";
    }

    public static function partial($view, $data = []) {
        extract($data);
        require __DIR__ . "/../views/{$view}.php";
    }
}
