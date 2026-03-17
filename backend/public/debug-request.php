<?php
/**
 * Script de debug para verificar como o Laravel processa a requisição
 * Acesse: https://fitzone.wuaze.com/backend/public/debug-request.php
 */

header('Content-Type: application/json');

// Carregar Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Capturar requisição
$request = \Illuminate\Http\Request::capture();

// Informações da requisição
$info = [
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'path_info' => $_SERVER['PATH_INFO'] ?? 'N/A',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    'request_path' => $request->path(),
    'request_getPathInfo' => $request->getPathInfo(),
    'request_getRequestUri' => $request->getRequestUri(),
    'request_is_api' => $request->is('api/*'),
    'request_expects_json' => $request->expectsJson(),
];

// Tentar fazer match com a rota de login
$router = $app->make('router');
$routes = $router->getRoutes();

// Criar uma requisição de teste para /api/v1/login
$testRequest = \Illuminate\Http\Request::create('/api/v1/login', 'POST');
$testRequest2 = \Illuminate\Http\Request::create('api/v1/login', 'POST');

$info['test_request_1_path'] = $testRequest->path();
$info['test_request_2_path'] = $testRequest2->path();

// Tentar encontrar a rota
try {
    $match1 = $router->getRoutes()->match($testRequest);
    $info['match_1'] = [
        'uri' => $match1->uri(),
        'methods' => $match1->methods(),
        'action' => $match1->getActionName(),
    ];
} catch (\Exception $e) {
    $info['match_1_error'] = $e->getMessage();
}

try {
    $match2 = $router->getRoutes()->match($testRequest2);
    $info['match_2'] = [
        'uri' => $match2->uri(),
        'methods' => $match2->methods(),
        'action' => $match2->getActionName(),
    ];
} catch (\Exception $e) {
    $info['match_2_error'] = $e->getMessage();
}

// Listar todas as rotas de API
$allRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/') === 0) {
        $allRoutes[] = [
            'methods' => $route->methods(),
            'uri' => $route->uri(),
            'name' => $route->getName(),
        ];
    }
}

$info['all_api_routes'] = $allRoutes;

echo json_encode($info, JSON_PRETTY_PRINT);
