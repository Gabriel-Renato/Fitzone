<?php
/**
 * FitZone - Router da API
 * Arquivo na raiz para capturar requisições /api/*
 */

// Capturar a URI da requisição
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Verificar se é uma requisição da API
if (strpos($requestUri, '/api/') !== 0) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not an API request']);
    exit;
}

// Manter o prefixo /api e garantir que tenha /v1
$apiPath = $requestUri;

// Garantir que comece com /api
if (strpos($apiPath, '/api/') !== 0) {
    $apiPath = '/api' . (substr($apiPath, 0, 1) !== '/' ? '/' : '') . $apiPath;
}

// Se não começar com /api/v1, adicionar /v1 após /api
if (strpos($apiPath, '/api/v1') !== 0) {
    // Remover /api temporariamente para adicionar /v1
    $pathWithoutApi = preg_replace('#^/api#', '', $apiPath);
    $apiPath = '/api/v1' . ($pathWithoutApi === '/' ? '' : $pathWithoutApi);
}

// Caminho para o Laravel
$laravelPath = __DIR__ . '/backend/public/index.php';

if (!file_exists($laravelPath)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Backend Laravel não encontrado',
        'path' => $laravelPath
    ]);
    exit;
}

// Mudar para o diretório do Laravel
chdir(dirname($laravelPath));

// Ajustar variáveis de ambiente
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_URI'] = $apiPath;
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = dirname($laravelPath);
$_SERVER['SCRIPT_FILENAME'] = $laravelPath;

// Incluir o Laravel
require $laravelPath;


