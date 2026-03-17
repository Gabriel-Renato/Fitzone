<?php
/**
 * Verificar se os controllers existem
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$controllers = [
    'App\\Http\\Controllers\\Api\\AuthController',
    'App\\Http\\Controllers\\Api\\ExerciseController',
    'App\\Http\\Controllers\\Api\\WorkoutController',
    'App\\Http\\Controllers\\Api\\WorkoutPlanController',
    'App\\Http\\Controllers\\Api\\ClientController',
    'App\\Http\\Controllers\\Api\\WorkoutLogController',
];

$results = [];

// Verificar arquivos físicos
$controllerFiles = [
    '../app/Http/Controllers/Api/AuthController.php',
    '../app/Http/Controllers/Api/ExerciseController.php',
    '../app/Http/Controllers/Api/WorkoutController.php',
    '../app/Http/Controllers/Api/WorkoutPlanController.php',
    '../app/Http/Controllers/Api/ClientController.php',
    '../app/Http/Controllers/Api/WorkoutLogController.php',
];

foreach ($controllerFiles as $file) {
    $path = __DIR__ . '/' . $file;
    $results['files'][$file] = [
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'path' => realpath($path) ?: 'NOT FOUND',
    ];
}

// Tentar carregar Laravel e verificar classes
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    foreach ($controllers as $controller) {
        try {
            $reflection = new ReflectionClass($controller);
            $results['classes'][$controller] = [
                'exists' => true,
                'has_login_method' => $reflection->hasMethod('login'),
            ];
        } catch (\Exception $e) {
            $results['classes'][$controller] = [
                'exists' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    // Tentar carregar routes/api.php
    $apiRoutesFile = __DIR__ . '/../routes/api.php';
    if (file_exists($apiRoutesFile)) {
        try {
            // Simular o ambiente do Route facade
            $router = $app->make('router');
            
            // Tentar incluir o arquivo de rotas
            ob_start();
            include $apiRoutesFile;
            $output = ob_get_clean();
            
            $routes = $router->getRoutes();
            $results['routes_after_include'] = [
                'count' => count($routes),
                'output' => $output,
            ];
            
        } catch (\Exception $e) {
            $results['routes_include_error'] = $e->getMessage();
        }
    }
    
} catch (\Exception $e) {
    $results['laravel_error'] = $e->getMessage();
    $results['laravel_trace'] = $e->getTraceAsString();
}

echo json_encode($results, JSON_PRETTY_PRINT);
