<?php
/**
 * FitZone - Router da API para InfinityFree
 * Este arquivo funciona mesmo sem mod_rewrite
 * Acesse via: /api/index.php/v1/exercises
 */

// Log de debug - usar caminho absoluto
$logDir = dirname(__DIR__) . '/backend/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/api-debug.log';

// Garantir que o arquivo existe e tem permissões
if (!file_exists($logFile)) {
    @touch($logFile);
    @chmod($logFile, 0666);
}

$logMessage = date('Y-m-d H:i:s') . " - API Request\n";
$logMessage .= "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
$logMessage .= "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'N/A') . "\n";
$logMessage .= "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
$logMessage .= "HTTP_ACCEPT: " . ($_SERVER['HTTP_ACCEPT'] ?? 'N/A') . "\n";
$logMessage .= "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
$logMessage .= "---\n";

// Tentar escrever no log
$writeResult = @file_put_contents($logFile, $logMessage, FILE_APPEND);
if ($writeResult === false) {
    // Se falhar, tentar criar arquivo de erro alternativo
    error_log("Falha ao escrever em api-debug.log. Caminho: $logFile");
}

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

// Capturar método HTTP e dados
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$query = $_GET ?? [];
$body = file_get_contents('php://input');

// Limpar query string da URI se houver
$apiPathClean = parse_url($apiPath, PHP_URL_PATH);

// Log final antes de passar para Laravel
file_put_contents($logFile, "Final API Path (clean): $apiPathClean\n", FILE_APPEND);
file_put_contents($logFile, "REQUEST_METHOD: $method\n", FILE_APPEND);
file_put_contents($logFile, "Body length: " . strlen($body) . "\n", FILE_APPEND);
file_put_contents($logFile, "========================================\n\n", FILE_APPEND);

// Carregar Laravel diretamente (não via require do index.php)
require __DIR__ . '/../backend/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../backend/bootstrap/app.php';

// IMPORTANTE: Passar o caminho completo /api/v1/login
// O Laravel reconhece automaticamente rotas que começam com /api
$laravelPath = $apiPathClean; // Já está como /api/v1/login

// Log do caminho que será passado ao Laravel
file_put_contents($logFile, "Laravel Path (full): $laravelPath\n", FILE_APPEND);

// Ajustar $_SERVER para que o Laravel reconheça como requisição da API
$_SERVER['REQUEST_URI'] = $laravelPath;
$_SERVER['PATH_INFO'] = $laravelPath;

// Criar requisição explicitamente
$request = \Illuminate\Http\Request::create(
    $laravelPath,    // URI: /api/v1/login (caminho completo)
    $method,         // POST
    $query,          // Query parameters
    $_COOKIE ?? [],  // Cookies
    $_FILES ?? [],   // Files
    $_SERVER,        // Server vars
    $body            // Request body
);

// Log adicional
file_put_contents($logFile, "Request path(): " . $request->path() . "\n", FILE_APPEND);
file_put_contents($logFile, "Request is('api/*'): " . ($request->is('api/*') ? 'yes' : 'no') . "\n", FILE_APPEND);
file_put_contents($logFile, "Request uri(): " . $request->getRequestUri() . "\n", FILE_APPEND);

// Processar requisição - o Laravel envia a resposta automaticamente
try {
    $response = $app->handleRequest($request);
    // Se houver resposta, enviar (pode já ter sido enviada)
    if ($response && !headers_sent()) {
        $response->send();
    }
} catch (\Throwable $e) {
    // Se houver erro, logar e retornar JSON de erro
    file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar requisição',
            'error' => $e->getMessage()
        ]);
    }
}
exit;
