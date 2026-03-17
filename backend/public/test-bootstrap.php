<?php
/**
 * Teste do bootstrap do Laravel
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$results = [];

// 1. Verificar se arquivos existem
$files = [
    '../vendor/autoload.php',
    '../bootstrap/app.php',
    '../routes/api.php',
    '../app/Http/Controllers/Api/AuthController.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $results['files'][$file] = [
        'exists' => file_exists($path),
        'path' => realpath($path) ?: 'NOT FOUND',
    ];
}

// 2. Tentar carregar autoload
try {
    require __DIR__.'/../vendor/autoload.php';
    $results['autoload'] = 'OK';
} catch (\Exception $e) {
    $results['autoload'] = 'ERROR: ' . $e->getMessage();
}

// 3. Tentar carregar bootstrap
try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $results['bootstrap'] = 'OK';
    $results['version'] = $app->version();
    
    // 4. Verificar se rotas estão sendo carregadas
    $router = $app->make('router');
    $routes = $router->getRoutes();
    $results['routes_count'] = count($routes);
    
    // 5. Tentar carregar routes/api.php manualmente
    try {
        $apiRoutesContent = file_get_contents(__DIR__ . '/../routes/api.php');
        $results['api_routes_file'] = [
            'readable' => true,
            'size' => strlen($apiRoutesContent),
            'has_login' => strpos($apiRoutesContent, "Route::post('login'") !== false,
        ];
        
        // Tentar executar o arquivo de rotas
        ob_start();
        $oldRoutes = $router->getRoutes();
        $oldCount = count($oldRoutes);
        
        // Incluir o arquivo de rotas
        include __DIR__ . '/../routes/api.php';
        
        $newRoutes = $router->getRoutes();
        $newCount = count($newRoutes);
        
        $output = ob_get_clean();
        
        $results['manual_route_load'] = [
            'old_count' => $oldCount,
            'new_count' => $newCount,
            'output' => $output,
        ];
        
    } catch (\Exception $e) {
        $results['api_routes_file'] = [
            'error' => $e->getMessage(),
        ];
    }
    
} catch (\Exception $e) {
    $results['bootstrap'] = 'ERROR: ' . $e->getMessage();
    $results['bootstrap_trace'] = $e->getTraceAsString();
}

echo json_encode($results, JSON_PRETTY_PRINT);
