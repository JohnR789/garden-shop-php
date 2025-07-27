<?php
// Main entry point and router bootstrap
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// Start or resume session for cart/user login state
session_start();

// Dispatch to appropriate controller/action
$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
