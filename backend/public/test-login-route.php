<?php
/**
 * Script para testar a rota de login diretamente
 * Simula uma requisição POST para /api/v1/login
 */

header('Content-Type: application/json');

// Carregar Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Simular requisição POST para /api/v1/login
$testPaths = [
    '/api/v1/login',
    'api/v1/login',
    '/backend/public/api/v1/login',
    'backend/public/api/v1/login',
];

$results = [];

foreach ($testPaths as $testPath) {
    $request = \Illuminate\Http\Request::create($testPath, 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ], json_encode(['email' => 'test@test.com', 'password' => 'test']));
    
    $router = $app->make('router');
    
    try {
        $route = $router->getRoutes()->match($request);
        $results[$testPath] = [
            'success' => true,
            'route_uri' => $route->uri(),
            'route_methods' => $route->methods(),
            'route_action' => $route->getActionName(),
            'request_path' => $request->path(),
            'request_getPathInfo' => $request->getPathInfo(),
        ];
    } catch (\Exception $e) {
        $results[$testPath] = [
            'success' => false,
            'error' => $e->getMessage(),
            'request_path' => $request->path(),
            'request_getPathInfo' => $request->getPathInfo(),
        ];
    }
}

// Listar todas as rotas registradas
$router = $app->make('router');
$allRoutes = [];
foreach ($router->getRoutes() as $route) {
    $allRoutes[] = [
        'methods' => $route->methods(),
        'uri' => $route->uri(),
        'name' => $route->getName(),
    ];
}

echo json_encode([
    'test_results' => $results,
    'all_routes' => $allRoutes,
    'total_routes' => count($allRoutes),
], JSON_PRETTY_PRINT);
