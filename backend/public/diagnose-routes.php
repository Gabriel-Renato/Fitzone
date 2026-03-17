<?php
/**
 * Diagnóstico de rotas sem usar exec()
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$results = [];

// 1. Verificar arquivos
$apiRoutesFile = __DIR__ . '/../routes/api.php';
$results['api_routes_file'] = [
    'exists' => file_exists($apiRoutesFile),
    'readable' => is_readable($apiRoutesFile),
    'path' => realpath($apiRoutesFile) ?: 'NOT FOUND',
];

if (file_exists($apiRoutesFile)) {
    $content = file_get_contents($apiRoutesFile);
    $results['api_routes_file']['size'] = strlen($content);
    $results['api_routes_file']['has_login'] = strpos($content, "Route::post('login'") !== false;
    $results['api_routes_file']['has_auth_controller'] = strpos($content, 'AuthController') !== false;
}

// 2. Carregar Laravel
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $results['laravel'] = [
        'loaded' => true,
        'version' => $app->version(),
    ];
    
    // 3. Verificar rotas ANTES de tentar carregar
    $router = $app->make('router');
    $routesBefore = $router->getRoutes();
    $results['routes_before'] = count($routesBefore);
    
    // 4. Verificar se o arquivo de rotas pode ser incluído
    // O problema pode ser que o Route facade não está disponível quando o arquivo é incluído
    try {
        // Verificar se Route facade está disponível
        $routeFacade = \Illuminate\Support\Facades\Route::class;
        $results['route_facade'] = 'Available';
        
        // Tentar registrar uma rota de teste diretamente
        \Illuminate\Support\Facades\Route::get('/test-route', function() {
            return 'test';
        });
        
        $routesAfter = $router->getRoutes();
        $results['routes_after_manual'] = count($routesAfter);
        $results['manual_route_works'] = count($routesAfter) > count($routesBefore);
        
    } catch (\Exception $e) {
        $results['route_facade_error'] = $e->getMessage();
    }
    
    // 5. Verificar o que está no bootstrap/app.php
    $bootstrapFile = __DIR__ . '/../bootstrap/app.php';
    $bootstrapContent = file_get_contents($bootstrapFile);
    $results['bootstrap'] = [
        'has_withRouting' => strpos($bootstrapContent, 'withRouting') !== false,
        'has_api_routes' => strpos($bootstrapContent, "api: __DIR__.'/../routes/api.php'") !== false,
    ];
    
    // 6. Tentar limpar cache manualmente (sem exec)
    $cacheFiles = [
        '../bootstrap/cache/config.php',
        '../bootstrap/cache/routes-v7.php',
        '../bootstrap/cache/routes.php',
    ];
    
    $results['cache_files'] = [];
    foreach ($cacheFiles as $cacheFile) {
        $path = __DIR__ . '/' . $cacheFile;
        if (file_exists($path)) {
            // Tentar deletar
            @unlink($path);
            $results['cache_files'][$cacheFile] = [
                'existed' => true,
                'deleted' => !file_exists($path),
            ];
        } else {
            $results['cache_files'][$cacheFile] = [
                'existed' => false,
            ];
        }
    }
    
    // 7. Recarregar Laravel após limpar cache
    $app2 = require_once __DIR__.'/../bootstrap/app.php';
    $router2 = $app2->make('router');
    $routes2 = $router2->getRoutes();
    $results['routes_after_cache_clear'] = count($routes2);
    
    // 8. Listar rotas encontradas
    $allRoutes = [];
    foreach ($routes2 as $route) {
        $allRoutes[] = [
            'methods' => $route->methods(),
            'uri' => $route->uri(),
            'name' => $route->getName(),
        ];
    }
    $results['all_routes'] = $allRoutes;
    
} catch (\Throwable $e) {
    $results['error'] = $e->getMessage();
    $results['trace'] = $e->getTraceAsString();
}

echo json_encode($results, JSON_PRETTY_PRINT);
