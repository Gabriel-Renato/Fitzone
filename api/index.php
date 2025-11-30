<?php
/**
 * FitZone - Router da API para InfinityFree
 * Este arquivo funciona mesmo sem mod_rewrite
 * Acesse via: /api/index.php/v1/exercises
 */

// Capturar a URI da requisição
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Processar a URI quando acessada via /api/index.php/v1/exercises
// O PATH_INFO contém a parte após /api/index.php
$pathInfo = $_SERVER['PATH_INFO'] ?? '';

// Se tiver PATH_INFO, usar ele (ex: /v1/exercises)
if ($pathInfo) {
    $apiPath = '/api' . $pathInfo;
} else {
    // Caso contrário, processar a REQUEST_URI completa
    // Remover /api/index.php da URI se presente
    $apiPath = preg_replace('#^/api/index\.php#', '/api', $requestUri);
}

// Se não começar com /api/v1, adicionar /v1 após /api
if (strpos($apiPath, '/api/v1') !== 0) {
    // Remover /api temporariamente para adicionar /v1
    $pathWithoutApi = preg_replace('#^/api#', '', $apiPath);
    $apiPath = '/api/v1' . ($pathWithoutApi === '/' ? '' : $pathWithoutApi);
}

// Caminho para o Laravel
$laravelPath = dirname(__DIR__) . '/backend/public/index.php';

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
