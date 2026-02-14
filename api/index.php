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

// Processar a URI quando acessada via /api/index.php/v1/login
// O Laravel espera /api/v1/login (com /api no início)

// Primeiro, tentar extrair do PATH_INFO (mais confiável)
$pathInfo = $_SERVER['PATH_INFO'] ?? '';

// Se não tiver PATH_INFO, tentar extrair da REQUEST_URI
if (empty($pathInfo) || $pathInfo === '/') {
    // Extrair a parte após /api/index.php da REQUEST_URI
    if (preg_match('#/api/index\.php(/.*)$#', $requestUri, $matches)) {
        $pathInfo = $matches[1];
    } elseif (preg_match('#/api/index\.php$#', $requestUri)) {
        $pathInfo = '/';
    }
}

// Limpar query string do pathInfo se houver
if ($pathInfo && strpos($pathInfo, '?') !== false) {
    $pathInfo = strstr($pathInfo, '?', true);
}

// Construir o caminho da API
// O Laravel registra rotas da API com prefixo /api automaticamente
// Então precisamos passar /api/v1/login

if (empty($pathInfo) || $pathInfo === '/') {
    // Se não houver path, usar /api/v1 como padrão
    $apiPath = '/api/v1';
} else {
    // Garantir que pathInfo comece com /
    if (substr($pathInfo, 0, 1) !== '/') {
        $pathInfo = '/' . $pathInfo;
    }
    
    // Se já começar com /v1, adicionar /api antes
    if (strpos($pathInfo, '/v1') === 0) {
        $apiPath = '/api' . $pathInfo;
    } elseif (strpos($pathInfo, '/api/v1') === 0) {
        // Já está no formato correto
        $apiPath = $pathInfo;
    } elseif (strpos($pathInfo, '/api/') === 0) {
        // Se começar com /api/ mas não com /api/v1, assumir que precisa de /v1
        $pathAfterApi = substr($pathInfo, 4); // Remove /api
        $apiPath = '/api/v1' . ($pathAfterApi === '' ? '' : $pathAfterApi);
    } else {
        // Caso padrão: adicionar /api/v1
        $apiPath = '/api/v1' . $pathInfo;
    }
}

file_put_contents($logFile, "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'N/A') . "\n", FILE_APPEND);
file_put_contents($logFile, "REQUEST_URI: $requestUri\n", FILE_APPEND);
file_put_contents($logFile, "Processed pathInfo: $pathInfo\n", FILE_APPEND);
file_put_contents($logFile, "Final apiPath: $apiPath\n", FILE_APPEND);

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
// NÃO ler php://input aqui - deixar o Laravel ler diretamente
// $body = file_get_contents('php://input');

// Limpar query string da URI se houver
$apiPathClean = parse_url($apiPath, PHP_URL_PATH);

// GARANTIR que o caminho sempre comece com /
if (substr($apiPathClean, 0, 1) !== '/') {
    $apiPathClean = '/' . $apiPathClean;
}

// Log final antes de passar para Laravel
file_put_contents($logFile, "Final API Path (clean): $apiPathClean\n", FILE_APPEND);
file_put_contents($logFile, "REQUEST_METHOD: $method\n", FILE_APPEND);
file_put_contents($logFile, "========================================\n\n", FILE_APPEND);

// IMPORTANTE: Passar o caminho completo /api/v1/login
// O Laravel precisa do prefixo /api para reconhecer como rota da API
$laravelPath = $apiPathClean; // Já está como /api/v1/login

// Garantir que o caminho está correto
if (substr($laravelPath, 0, 1) !== '/') {
    $laravelPath = '/' . $laravelPath;
}
if (strpos($laravelPath, '/api/') !== 0) {
    if (strpos($laravelPath, '/v1/') === 0 || $laravelPath === '/v1') {
        $laravelPath = '/api' . $laravelPath;
    } else {
        $laravelPath = '/api/v1' . (substr($laravelPath, 0, 1) !== '/' ? '/' : '') . $laravelPath;
    }
}

file_put_contents($logFile, "Final Laravel Path: $laravelPath\n", FILE_APPEND);

// Ajustar $_SERVER para o Laravel capturar corretamente
$_SERVER['REQUEST_URI'] = $laravelPath;
$_SERVER['PATH_INFO'] = $laravelPath;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_METHOD'] = $method;

// Garantir headers importantes
if (!isset($_SERVER['HTTP_ACCEPT']) || empty($_SERVER['HTTP_ACCEPT'])) {
    $_SERVER['HTTP_ACCEPT'] = 'application/json';
}
// Garantir CONTENT_TYPE se for POST/PUT/PATCH
if (in_array($method, ['POST', 'PUT', 'PATCH']) && !isset($_SERVER['CONTENT_TYPE'])) {
    $_SERVER['CONTENT_TYPE'] = 'application/json';
}

file_put_contents($logFile, "HTTP_ACCEPT: " . ($_SERVER['HTTP_ACCEPT'] ?? 'N/A') . "\n", FILE_APPEND);
file_put_contents($logFile, "CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A') . "\n", FILE_APPEND);

// Usar o index.php do Laravel (garante que tudo seja inicializado corretamente)
$laravelPublicPath = dirname(__DIR__) . '/backend/public';
chdir($laravelPublicPath);

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_URI'] = $laravelPath;
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = $laravelPublicPath;
$_SERVER['SCRIPT_FILENAME'] = $laravelPublicPath . '/index.php';

require $laravelPublicPath . '/index.php';
exit;
