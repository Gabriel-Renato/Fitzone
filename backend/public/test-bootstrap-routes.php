<?php
/**
 * Testar bootstrap e carregamento de rotas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$results = [];

try {
    // 1. Limpar cache primeiro
    $cacheFiles = [
        '../bootstrap/cache/config.php',
        '../bootstrap/cache/routes-v7.php',
        '../bootstrap/cache/routes.php',
    ];
    
    foreach ($cacheFiles as $file) {
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            @unlink($path);
        }
    }
    
    // 2. Carregar Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $results['bootstrap'] = [
        'loaded' => true,
        'version' => $app->version(),
    ];
    
    // 3. Verificar se o arquivo de rotas existe e é legível
    $apiRoutesFile = __DIR__ . '/../routes/api.php';
    $results['routes_file'] = [
        'exists' => file_exists($apiRoutesFile),
        'readable' => is_readable($apiRoutesFile),
        'path' => realpath($apiRoutesFile) ?: 'NOT FOUND',
    ];
    
    // 4. Verificar rotas ANTES de tentar recarregar
    $router = $app->make('router');
    $routesBefore = $router->getRoutes();
    $results['routes_before'] = count($routesBefore);
    
    // 5. Tentar recarregar as rotas manualmente
    // O problema pode ser que o Laravel não está carregando o arquivo de rotas
    // Vamos verificar se o bootstrap/app.php está configurado corretamente
    $bootstrapFile = __DIR__ . '/../bootstrap/app.php';
    $bootstrapContent = file_get_contents($bootstrapFile);
    
    $results['bootstrap_config'] = [
        'has_withRouting' => strpos($bootstrapContent, 'withRouting') !== false,
        'has_api_routes' => strpos($bootstrapContent, "api: __DIR__.'/../routes/api.php'") !== false,
    ];
    
    // 6. Verificar se há erros ao carregar rotas
    // Vamos tentar incluir o arquivo de rotas dentro do contexto do Laravel
    try {
        // Definir o contexto correto para as facades
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        // Agora tentar incluir o arquivo de rotas
        // Mas primeiro, vamos verificar se o Route facade está disponível
        if (class_exists('Illuminate\Support\Facades\Route')) {
            $results['route_facade'] = 'Available';
            
            // Tentar registrar uma rota de teste
            \Illuminate\Support\Facades\Route::get('/test-bootstrap', function() {
                return 'test';
            });
            
            $routesAfter = $router->getRoutes();
            $results['routes_after_manual'] = count($routesAfter);
        } else {
            $results['route_facade'] = 'Not Available';
        }
        
    } catch (\Exception $e) {
        $results['route_load_error'] = $e->getMessage();
    }
    
    // 7. Verificar rotas finais
    $routesFinal = $router->getRoutes();
    $results['routes_final'] = count($routesFinal);
    
    // 8. Listar todas as rotas
    $allRoutes = [];
    foreach ($routesFinal as $route) {
        $allRoutes[] = [
            'methods' => $route->methods(),
            'uri' => $route->uri(),
            'name' => $route->getName(),
        ];
    }
    
    $results['all_routes'] = $allRoutes;
    
    // Filtrar rotas de API
    $apiRoutes = array_filter($allRoutes, function($r) {
        return strpos($r['uri'], 'api/') === 0 || strpos($r['uri'], 'v1/') === 0;
    });
    
    $results['api_routes'] = array_values($apiRoutes);
    $results['api_routes_count'] = count($apiRoutes);
    
    // 9. Verificar se a rota de login está presente
    $loginRoutes = array_filter($allRoutes, function($r) {
        return strpos($r['uri'], 'login') !== false;
    });
    
    $results['login_routes'] = array_values($loginRoutes);
    
    // 10. Testar match da rota
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
    $results['file'] = $e->getFile();
    $results['line'] = $e->getLine();
}

echo json_encode($results, JSON_PRETTY_PRINT);
