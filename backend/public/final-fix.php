<?php
/**
 * Correção final - limpar cache e verificar rotas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$results = [];

// 1. Limpar TODOS os arquivos de cache manualmente
$cacheFiles = [
    '../bootstrap/cache/config.php',
    '../bootstrap/cache/routes-v7.php',
    '../bootstrap/cache/routes.php',
    '../bootstrap/cache/packages.php',
    '../bootstrap/cache/services.php',
];

$deletedCount = 0;
foreach ($cacheFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        if (@unlink($path)) {
            $deletedCount++;
        }
    }
}

$results['cache_cleared'] = [
    'files_deleted' => $deletedCount,
    'total_checked' => count($cacheFiles),
];

// 2. Limpar cache de storage
$storageCacheDirs = [
    '../storage/framework/cache/data',
    '../storage/framework/views',
];

foreach ($storageCacheDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}

// 3. Carregar Laravel FRESCO (sem cache)
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $results['laravel'] = [
        'loaded' => true,
        'version' => $app->version(),
    ];
    
    // 4. Verificar rotas
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    $results['routes'] = [
        'total' => count($routes),
    ];
    
    // Listar todas as rotas
    $allRoutes = [];
    foreach ($routes as $route) {
        $allRoutes[] = [
            'methods' => $route->methods(),
            'uri' => $route->uri(),
            'name' => $route->getName(),
        ];
    }
    
    $results['all_routes'] = $allRoutes;
    
    // Filtrar rotas de API
    $apiRoutes = array_filter($allRoutes, function($r) {
        return strpos($r['uri'], 'api/') === 0;
    });
    
    $results['api_routes'] = array_values($apiRoutes);
    $results['api_routes_count'] = count($apiRoutes);
    
    // Verificar rota de login especificamente
    $loginRoutes = array_filter($allRoutes, function($r) {
        return strpos($r['uri'], 'login') !== false;
    });
    
    $results['login_routes'] = array_values($loginRoutes);
    
    // 5. Testar se a rota funciona
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
    $results['error'] = $e->getMessage();
    $results['trace'] = $e->getTraceAsString();
}

echo json_encode($results, JSON_PRETTY_PRINT);
