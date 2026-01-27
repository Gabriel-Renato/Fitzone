<?php
/**
 * Script para limpar todos os caches do Laravel
 * Acesse: https://fitzone.wuaze.com/backend/public/clear-cache.php
 */

header('Content-Type: application/json');

chdir(__DIR__ . '/../');

$commands = [
    'config:clear' => 'Limpar cache de configuração',
    'route:clear' => 'Limpar cache de rotas',
    'cache:clear' => 'Limpar cache geral',
    'view:clear' => 'Limpar cache de views',
];

$results = [];

foreach ($commands as $command => $description) {
    $output = [];
    $returnVar = 0;
    exec("php artisan {$command} 2>&1", $output, $returnVar);
    
    $results[$command] = [
        'description' => $description,
        'success' => $returnVar === 0,
        'output' => implode("\n", $output),
        'return_code' => $returnVar,
    ];
}

// Verificar rotas após limpar cache
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$router = $app->make('router');
$routes = $router->getRoutes();
$routeCount = count($routes);

$apiRoutes = array_filter($routes->getRoutes(), function($route) {
    return strpos($route->uri(), 'api/') === 0;
});

$results['routes_after_clear'] = [
    'total_routes' => $routeCount,
    'api_routes' => count($apiRoutes),
];

echo json_encode([
    'success' => true,
    'results' => $results,
], JSON_PRETTY_PRINT);
