<?php
/**
 * Script para forçar carregamento de rotas
 * Acesse: https://fitzone.wuaze.com/backend/public/fix-routes.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

chdir(__DIR__ . '/../');

$results = [];

// 1. Limpar TODOS os caches
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
    $results['cache_clear'][$command] = [
        'success' => $returnVar === 0,
        'output' => implode("\n", $output),
    ];
}

// 2. Verificar se routes/api.php existe e é legível
$apiRoutesFile = __DIR__ . '/../routes/api.php';
$results['api_routes_file'] = [
    'exists' => file_exists($apiRoutesFile),
    'readable' => is_readable($apiRoutesFile),
    'path' => realpath($apiRoutesFile) ?: 'NOT FOUND',
];

if (file_exists($apiRoutesFile)) {
    $content = file_get_contents($apiRoutesFile);
    $results['api_routes_file']['has_login'] = strpos($content, "Route::post('login'") !== false;
    $results['api_routes_file']['size'] = strlen($content);
}

// 3. Carregar Laravel
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $results['laravel'] = [
        'loaded' => true,
        'version' => $app->version(),
    ];
    
    // 4. Forçar carregamento de rotas
    $router = $app->make('router');
    
    // Verificar se as rotas estão sendo carregadas
    $routes = $router->getRoutes();
    $routeCount = count($routes);
    
    $results['routes_before'] = [
        'count' => $routeCount,
    ];
    
    // Se não houver rotas, tentar forçar o carregamento
    if ($routeCount === 0) {
        // Tentar registrar rotas manualmente
        try {
            $apiRoutes = require __DIR__ . '/../routes/api.php';
            $results['manual_load'] = 'Tentou carregar routes/api.php';
        } catch (\Exception $e) {
            $results['manual_load_error'] = $e->getMessage();
        }
        
        // Recarregar rotas
        $routes = $router->getRoutes();
        $routeCount = count($routes);
    }
    
    $results['routes_after'] = [
        'count' => $routeCount,
    ];
    
    // Listar rotas de API
    $apiRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'api/') === 0) {
            $apiRoutes[] = [
                'methods' => $route->methods(),
                'uri' => $route->uri(),
                'name' => $route->getName(),
            ];
        }
    }
    
    $results['api_routes'] = $apiRoutes;
    $results['api_routes_count'] = count($apiRoutes);
    
    // 5. Testar rota de login
    try {
        $testRequest = \Illuminate\Http\Request::create('/api/v1/login', 'POST');
        $matchedRoute = $router->getRoutes()->match($testRequest);
        $results['login_test'] = [
            'success' => true,
            'route_uri' => $matchedRoute->uri(),
            'route_methods' => $matchedRoute->methods(),
        ];
    } catch (\Exception $e) {
        $results['login_test'] = [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
    
} catch (\Throwable $e) {
    $results['laravel'] = [
        'loaded' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
