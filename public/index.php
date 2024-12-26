<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
ini_set('display_errors', 1);
error_reporting(E_ALL);
$routes = require __DIR__ . '/../config/routes.php';

$router = new Router($routes);
$router->handleRequest($_SERVER['REQUEST_URI']);
