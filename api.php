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

// Remover /api da URI
$apiPath = preg_replace('#^/api#', '', $requestUri);

// Garantir que comece com /
if (substr($apiPath, 0, 1) !== '/') {
    $apiPath = '/' . $apiPath;
}

// Se não começar com /v1, adicionar
if (substr($apiPath, 0, 3) !== '/v1') {
    $apiPath = '/v1' . $apiPath;
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

