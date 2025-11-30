<?php
/**
 * FitZone - Router da API para InfinityFree
 * Este arquivo funciona mesmo sem mod_rewrite
 * Acesse via: /api/index.php/v1/exercises
 */

// Log de debug
$logFile = __DIR__ . '/../backend/storage/logs/api-debug.log';
$logMessage = date('Y-m-d H:i:s') . " - API Request\n";
$logMessage .= "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
$logMessage .= "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'N/A') . "\n";
$logMessage .= "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
$logMessage .= "HTTP_ACCEPT: " . ($_SERVER['HTTP_ACCEPT'] ?? 'N/A') . "\n";
$logMessage .= "---\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

// Capturar a URI da requisição
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Processar a URI quando acessada via /api/index.php/v1/exercises
// O PATH_INFO contém a parte após /api/index.php
$pathInfo = $_SERVER['PATH_INFO'] ?? '';

// IMPORTANTE: O Laravel registra rotas da API com prefixo /api
// Então precisamos passar /api/v1/... para o Laravel encontrar a rota

// Se tiver PATH_INFO, usar ele (ex: /v1/exercises)
if ($pathInfo && $pathInfo !== '/') {
    // PATH_INFO já vem como /v1/exercises, adicionar /api antes
    $apiPath = '/api' . $pathInfo;
    file_put_contents($logFile, "Using PATH_INFO: $pathInfo -> $apiPath\n", FILE_APPEND);
} else {
    // Caso contrário, processar a REQUEST_URI completa
    // Extrair a parte após /api/index.php
    if (preg_match('#^/api/index\.php(/.*)$#', $requestUri, $matches)) {
        // Se a URI for /api/index.php/v1/login, pegar /v1/login
        $apiPath = '/api' . $matches[1];
        file_put_contents($logFile, "Extracted from REQUEST_URI: {$matches[1]} -> $apiPath\n", FILE_APPEND);
    } else {
        // Fallback: remover /api/index.php da URI se presente
        $apiPath = preg_replace('#^/api/index\.php#', '/api', $requestUri);
        
        // Garantir que comece com /api
        if (strpos($apiPath, '/api') !== 0) {
            $apiPath = '/api' . (substr($apiPath, 0, 1) !== '/' ? '/' : '') . $apiPath;
        }
        file_put_contents($logFile, "Fallback processing: $apiPath\n", FILE_APPEND);
    }
}

// Garantir que comece com /
if (substr($apiPath, 0, 1) !== '/') {
    $apiPath = '/' . $apiPath;
}

// Se não começar com /api/v1, adicionar /v1 após /api
if (strpos($apiPath, '/api/v1') !== 0) {
    // Remover /api temporariamente para adicionar /v1
    $pathWithoutApi = preg_replace('#^/api#', '', $apiPath);
    $apiPath = '/api/v1' . ($pathWithoutApi === '/' ? '' : $pathWithoutApi);
}

// Log do caminho processado
file_put_contents($logFile, "Processed API Path (for Laravel): $apiPath\n---\n", FILE_APPEND);

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
