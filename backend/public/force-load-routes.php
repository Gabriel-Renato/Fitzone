<?php
/**
 * Forçar carregamento de rotas e diagnosticar problemas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

header('Content-Type: application/json');

$results = [];

try {
    // 1. Limpar TODOS os caches
    $cacheFiles = [
        '../bootstrap/cache/config.php',
        '../bootstrap/cache/routes-v7.php',
        '../bootstrap/cache/routes.php',
        '../bootstrap/cache/packages.php',
        '../bootstrap/cache/services.php',
    ];
    
    $deleted = 0;
    foreach ($cacheFiles as $file) {
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            if (@unlink($path)) {
                $deleted++;
            }
        }
    }
    $results['cache_cleared'] = $deleted;
    
    // 2. Carregar Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $results['laravel'] = [
        'loaded' => true,
        'version' => $app->version(),
    ];
    
    // 3. Verificar rotas ANTES
    $router = $app->make('router');
    $routesBefore = $router->getRoutes();
    $results['routes_before'] = count($routesBefore);
    
    // 4. Verificar se o arquivo de rotas pode ser lido
    $apiRoutesFile = __DIR__ . '/../routes/api.php';
    $results['routes_file'] = [
        'exists' => file_exists($apiRoutesFile),
        'readable' => is_readable($apiRoutesFile),
        'size' => file_exists($apiRoutesFile) ? filesize($apiRoutesFile) : 0,
    ];
    
    // 5. Verificar se há erros de sintaxe no arquivo de rotas
    $routesContent = file_get_contents($apiRoutesFile);
    $results['routes_file_content'] = [
        'has_route_prefix' => strpos($routesContent, "Route::prefix('v1')") !== false,
        'has_login' => strpos($routesContent, "Route::post('login'") !== false,
        'has_auth_controller' => strpos($routesContent, 'AuthController') !== false,
    ];
    
    // 6. Verificar se os controllers existem e podem ser carregados
    $controllers = [
        'App\\Http\\Controllers\\Api\\AuthController',
        'App\\Http\\Controllers\\Api\\ExerciseController',
    ];
    
    $results['controllers'] = [];
    foreach ($controllers as $controller) {
        try {
            if (class_exists($controller)) {
                $reflection = new ReflectionClass($controller);
                $results['controllers'][$controller] = [
                    'exists' => true,
                    'has_login' => $reflection->hasMethod('login'),
                ];
            } else {
                $results['controllers'][$controller] = [
                    'exists' => false,
                ];
            }
        } catch (\Exception $e) {
            $results['controllers'][$controller] = [
                'error' => $e->getMessage(),
            ];
        }
    }
    
    // 7. Tentar recarregar o bootstrap (forçar recarregamento de rotas)
    // O problema pode ser que o Laravel não está carregando as rotas na primeira vez
    // Vamos tentar criar uma nova instância do Application
    
    // Limpar cache novamente
    foreach ($cacheFiles as $file) {
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            @unlink($path);
        }
    }
    
    // Recarregar completamente
    $app2 = require_once __DIR__.'/../bootstrap/app.php';
    $router2 = $app2->make('router');
    
    // Forçar inicialização do kernel HTTP (isso carrega as rotas)
    $kernel = $app2->make(\Illuminate\Contracts\Http\Kernel::class);
    
    // Criar uma requisição de teste para forçar o carregamento
    $testRequest = \Illuminate\Http\Request::create('/api/v1/login', 'POST');
    
    // Processar a requisição (isso força o carregamento de rotas)
    try {
        $response = $kernel->handle($testRequest);
        $results['kernel_handle'] = 'Success';
    } catch (\Exception $e) {
        $results['kernel_handle'] = [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];
    }
    
    // Verificar rotas DEPOIS
    $routesAfter = $router2->getRoutes();
    $results['routes_after'] = count($routesAfter);
    
    // 8. Listar todas as rotas
    $allRoutes = [];
    foreach ($routesAfter as $route) {
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
    
    // 9. Verificar rota de login especificamente
    $loginRoutes = array_filter($allRoutes, function($r) {
        return strpos($r['uri'], 'login') !== false;
    });
    
    $results['login_routes'] = array_values($loginRoutes);
    
    // 10. Tentar fazer match da rota
    try {
        $testRequest2 = \Illuminate\Http\Request::create('/api/v1/login', 'POST');
        $matchedRoute = $router2->getRoutes()->match($testRequest2);
        $results['login_match'] = [
            'success' => true,
            'uri' => $matchedRoute->uri(),
            'methods' => $matchedRoute->methods(),
        ];
    } catch (\Exception $e) {
        $results['login_match'] = [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
    
} catch (\Throwable $e) {
    $results['error'] = $e->getMessage();
    $results['trace'] = $e->getTraceAsString();
    $results['file'] = $e->getFile();
    $results['line'] = $e->getLine();
}

echo json_encode($results, JSON_PRETTY_PRINT);
