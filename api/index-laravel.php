<?php
/**
 * Versão alternativa que cria a requisição explicitamente
 * Use este arquivo se api/index.php não funcionar
 */

// Capturar a URI da requisição
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$pathInfo = $_SERVER['PATH_INFO'] ?? '';

// Processar URI
if ($pathInfo && $pathInfo !== '/') {
    $apiPath = '/api' . $pathInfo;
} elseif (preg_match('#^/api/index\.php(/.*)$#', $requestUri, $matches)) {
    $apiPath = '/api' . $matches[1];
} else {
    $apiPath = preg_replace('#^/api/index\.php#', '/api', $requestUri);
}

if (strpos($apiPath, '/api/v1') !== 0) {
    $pathWithoutApi = preg_replace('#^/api#', '', $apiPath);
    $apiPath = '/api/v1' . ($pathWithoutApi === '/' ? '' : $pathWithoutApi);
}

// Caminho para o Laravel
$laravelPath = dirname(__DIR__) . '/backend/public/index.php';

if (!file_exists($laravelPath)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Backend Laravel não encontrado']);
    exit;
}

chdir(dirname($laravelPath));

// Carregar Laravel
require __DIR__ . '/../backend/vendor/autoload.php';
$app = require_once __DIR__ . '/../backend/bootstrap/app.php';

// Criar requisição explicitamente
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$query = $_GET ?? [];
$request = \Illuminate\Http\Request::create($apiPath, $method, $query, $_COOKIE ?? [], $_FILES ?? [], $_SERVER, file_get_contents('php://input'));

// Processar requisição
$response = $app->handleRequest($request);
$response->send();

