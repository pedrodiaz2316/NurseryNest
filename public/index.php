<?php

declare(strict_types=1);

use NurseryNest\Core\Env;
use NurseryNest\Core\Router;
use NurseryNest\Core\Session;

$root = dirname(__DIR__);

// Let PHP's built-in dev server serve static files directly.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($file !== __DIR__ . '/' && is_file($file)) {
        return false;
    }
}

require $root . '/vendor/autoload.php';

Env::load($root . '/.env');

error_reporting(E_ALL);
ini_set('display_errors', Env::bool('APP_DEBUG') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', $root . '/storage/logs/php-error.log');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; form-action 'self'; frame-ancestors 'none'");
if (str_starts_with(Env::get('APP_URL'), 'https://')) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
Session::start(Session::areaForPath($path));

$router = new Router();
(require $root . '/config/routes.php')($router);
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);
