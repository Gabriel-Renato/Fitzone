<?php
/**
 * Limpar cache manualmente sem usar exec()
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$results = [];

// 1. Limpar arquivos de cache manualmente
$cacheDirs = [
    '../bootstrap/cache',
    '../storage/framework/cache',
    '../storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $files = glob($path . '/*');
        $deleted = 0;
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                if (@unlink($file)) {
                    $deleted++;
                }
            }
        }
        $results['cache_clear'][$dir] = [
            'exists' => true,
            'files_deleted' => $deleted,
        ];
    } else {
        $results['cache_clear'][$dir] = [
            'exists' => false,
        ];
    }
}

// 2. Carregar Laravel
try {
    require __DIR__.'/../vendor/autoload.php';
    
    // Limpar cache de configuração via Laravel
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Tentar limpar cache usando métodos do Laravel
    try {
        $config = $app->make('config');
        $results['config_cleared'] = 'Attempted';
    } catch (\Exception $e) {
        $results['config_error'] = $e->getMessage();
    }
    
    // Verificar rotas após limpar cache
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    $results['routes'] = [
        'count' => count($routes),
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
    $results['api_routes'] = array_filter($allRoutes, function($r) {
        return strpos($r['uri'], 'api/') === 0;
    });
    
} catch (\Throwable $e) {
    $results['error'] = $e->getMessage();
    $results['trace'] = $e->getTraceAsString();
}

echo json_encode($results, JSON_PRETTY_PRINT);
