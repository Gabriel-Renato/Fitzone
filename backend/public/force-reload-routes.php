<?php
/**
 * Script para forçar recarregamento de rotas e limpar todos os caches
 * Acesse: https://fitzone.wuaze.com/backend/public/force-reload-routes.php
 */

header('Content-Type: application/json');

chdir(__DIR__ . '/../');

$results = [];

// 1. Limpar todos os caches
$commands = [
    'config:clear',
    'route:clear',
    'cache:clear',
    'view:clear',
];

foreach ($commands as $command) {
    $output = [];
    $returnVar = 0;
    exec("php artisan {$command} 2>&1", $output, $returnVar);
    $results[$command] = [
        'success' => $returnVar === 0,
        'output' => implode("\n", $output),
    ];
}

// 2. Carregar Laravel e verificar rotas
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. Forçar registro de rotas
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

$apiRoutes = array_filter($routeList, function($route) {
    return strpos($route['uri'], 'api/') === 0;
});

$loginRoutes = array_filter($routeList, function($route) {
    return strpos($route['uri'], 'login') !== false;
});

$results['routes'] = [
    'total' => count($routeList),
    'api_count' => count($apiRoutes),
    'login_count' => count($loginRoutes),
    'api_routes' => array_values($apiRoutes),
    'login_routes' => array_values($loginRoutes),
];

// 4. Testar requisição de login
try {
    $testRequest = \Illuminate\Http\Request::create('/api/v1/login', 'POST');
    $matchedRoute = $router->getRoutes()->match($testRequest);
    $results['login_route_test'] = [
        'success' => true,
        'route_uri' => $matchedRoute->uri(),
        'route_methods' => $matchedRoute->methods(),
    ];
} catch (\Exception $e) {
    $results['login_route_test'] = [
        'success' => false,
        'error' => $e->getMessage(),
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
