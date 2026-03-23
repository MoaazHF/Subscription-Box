<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/core/helpers.php';
require BASE_PATH . '/core/DatabaseManager.php';
require BASE_PATH . '/core/Session.php';
require BASE_PATH . '/core/Auth.php';
require BASE_PATH . '/core/Controller.php';
require BASE_PATH . '/core/Model.php';
require BASE_PATH . '/core/Router.php';
require BASE_PATH . '/app/Models/User.php';
require BASE_PATH . '/app/Controllers/HomeController.php';
require BASE_PATH . '/app/Controllers/AuthController.php';
require BASE_PATH . '/app/Controllers/DashboardController.php';
require BASE_PATH . '/app/Controllers/UserController.php';

Session::start();

$router = new Router();
require BASE_PATH . '/routes/web.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$basePath = parse_url(config('app')['base_url'], PHP_URL_PATH) ?? '';

if ($basePath !== '' && str_starts_with($requestUri, $basePath)) {
    $requestUri = substr($requestUri, strlen($basePath)) ?: '/';
}

$router->dispatch($requestUri, $_SERVER['REQUEST_METHOD']);

