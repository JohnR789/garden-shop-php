<?php
// Loads environment variables and returns database config array
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

return [
    'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
    'db_name' => $_ENV['DB_NAME'] ?? '',
    'db_user' => $_ENV['DB_USER'] ?? '',
    'db_pass' => $_ENV['DB_PASS'] ?? '',
];
