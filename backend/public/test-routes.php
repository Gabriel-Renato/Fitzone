<?php
/**
 * Script de teste para verificar rotas do Laravel
 * Acesse: https://fitzone.wuaze.com/backend/public/test-routes.php
 */

header('Content-Type: application/json');

// Carregar Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Verificar se .env existe
$envExists = file_exists(__DIR__.'/../.env');
$envExampleExists = file_exists(__DIR__.'/../.env.example');

// Listar rotas
$router = $app->make('router');
$routes = $router->getRoutes();

$routeList = [];
foreach ($routes as $route) {
    $routeList[] = [
        'methods' => $route->methods(),
        'uri' => $route->uri(),
        'name' => $route->getName(),
        'action' => $route->getActionName(),
    ];
}

// Filtrar apenas rotas de API
$apiRoutes = array_filter($routeList, function($route) {
    return strpos($route['uri'], 'api/') === 0;
});

// Verificar rota de login especificamente
$loginRoute = array_filter($routeList, function($route) {
    return strpos($route['uri'], 'login') !== false;
});

echo json_encode([
    'success' => true,
    'env_exists' => $envExists,
    'env_example_exists' => $envExampleExists,
    'total_routes' => count($routeList),
    'api_routes_count' => count($apiRoutes),
    'login_routes' => array_values($loginRoute),
    'api_routes' => array_values($apiRoutes),
    'php_version' => PHP_VERSION,
    'laravel_version' => $app->version(),
], JSON_PRETTY_PRINT);
