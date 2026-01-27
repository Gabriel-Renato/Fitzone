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
$body = file_get_contents('php://input');

// Limpar query string da URI se houver
$apiPathClean = parse_url($apiPath, PHP_URL_PATH);

// GARANTIR que o caminho sempre comece com /
if (substr($apiPathClean, 0, 1) !== '/') {
    $apiPathClean = '/' . $apiPathClean;
}

// Log final antes de passar para Laravel
file_put_contents($logFile, "Final API Path (clean): $apiPathClean\n", FILE_APPEND);
file_put_contents($logFile, "REQUEST_METHOD: $method\n", FILE_APPEND);
file_put_contents($logFile, "Body length: " . strlen($body) . "\n", FILE_APPEND);
file_put_contents($logFile, "========================================\n\n", FILE_APPEND);

// Carregar Laravel diretamente (não via require do index.php)
require __DIR__ . '/../backend/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../backend/bootstrap/app.php';

// IMPORTANTE: O Laravel registra rotas da API com prefixo /api automaticamente
// Então precisamos passar /api/v1/login (com /api no início)
$laravelPath = $apiPathClean; // Já está como /api/v1/login

// Log do caminho que será passado ao Laravel
file_put_contents($logFile, "Laravel Path (from apiPathClean): $laravelPath\n", FILE_APPEND);

// VALIDAÇÃO FINAL: Garantir que o caminho está no formato correto /api/v1/...
// IMPORTANTE: Sempre garantir que comece com /
if (substr($laravelPath, 0, 1) !== '/') {
    $laravelPath = '/' . $laravelPath;
}

if (strpos($laravelPath, '/api/v1/') === 0 || $laravelPath === '/api/v1') {
    // Caminho correto: /api/v1/login ou /api/v1
    file_put_contents($logFile, "Path format is correct: $laravelPath\n", FILE_APPEND);
} elseif (strpos($laravelPath, '/api/') === 0) {
    // Se começar com /api/ mas não com /api/v1/, pode ser /api/login
    // Neste caso, assumir que precisa de /v1
    if (strpos($laravelPath, '/api/v1') !== 0) {
        $pathWithoutApi = substr($laravelPath, 4); // Remove /api
        $laravelPath = '/api/v1' . ($pathWithoutApi === '' ? '' : $pathWithoutApi);
        file_put_contents($logFile, "Added /v1 to path: $laravelPath\n", FILE_APPEND);
    }
} elseif (strpos($laravelPath, '/v1/') === 0 || $laravelPath === '/v1') {
    // Se começar com /v1, adicionar /api antes
    $laravelPath = '/api' . $laravelPath;
    file_put_contents($logFile, "Added /api prefix: $laravelPath\n", FILE_APPEND);
} elseif (strpos($laravelPath, 'api/v1/') === 0) {
    // Se começar com api/v1/ (sem barra inicial), adicionar /
    $laravelPath = '/' . $laravelPath;
    file_put_contents($logFile, "Added leading slash: $laravelPath\n", FILE_APPEND);
} else {
    // Caso padrão: adicionar /api/v1
    $laravelPath = '/api/v1' . (substr($laravelPath, 0, 1) !== '/' ? '/' : '') . $laravelPath;
    file_put_contents($logFile, "Added /api/v1 prefix: $laravelPath\n", FILE_APPEND);
}

// GARANTIR novamente que comece com /
if (substr($laravelPath, 0, 1) !== '/') {
    $laravelPath = '/' . $laravelPath;
}

file_put_contents($logFile, "Final Laravel Path (after validation): $laravelPath\n", FILE_APPEND);

// Preparar $_SERVER com headers corretos para o Laravel reconhecer como API
$serverVars = $_SERVER;

// GARANTIR que o caminho sempre comece com / antes de passar para o Laravel
if (substr($laravelPath, 0, 1) !== '/') {
    $laravelPath = '/' . $laravelPath;
}

// IMPORTANTE: Definir REQUEST_URI e PATH_INFO ANTES de criar a requisição
// Isso garante que o Laravel processe o caminho corretamente
$serverVars['REQUEST_URI'] = $laravelPath;
$serverVars['PATH_INFO'] = $laravelPath;
$serverVars['SCRIPT_NAME'] = '/index.php';
$serverVars['PHP_SELF'] = '/index.php';

// Garantir headers importantes para o Laravel reconhecer como requisição da API
if (!isset($serverVars['HTTP_ACCEPT']) || empty($serverVars['HTTP_ACCEPT'])) {
    $serverVars['HTTP_ACCEPT'] = 'application/json';
}
if (!isset($serverVars['CONTENT_TYPE']) && !empty($body)) {
    $serverVars['CONTENT_TYPE'] = 'application/json';
}

file_put_contents($logFile, "HTTP_ACCEPT: " . ($serverVars['HTTP_ACCEPT'] ?? 'N/A') . "\n", FILE_APPEND);
file_put_contents($logFile, "CONTENT_TYPE: " . ($serverVars['CONTENT_TYPE'] ?? 'N/A') . "\n", FILE_APPEND);

// VALIDAÇÃO FINAL ABSOLUTA: Garantir que o caminho comece com /
// O Laravel precisa do caminho começando com / para encontrar as rotas
if (substr($laravelPath, 0, 1) !== '/') {
    $laravelPath = '/' . $laravelPath;
    file_put_contents($logFile, "CRITICAL: Added leading slash at final step: $laravelPath\n", FILE_APPEND);
}

// Log final antes de criar a requisição
file_put_contents($logFile, "Creating Laravel Request with path: $laravelPath\n", FILE_APPEND);
file_put_contents($logFile, "Request method: $method\n", FILE_APPEND);

// IMPORTANTE: Ajustar serverVars para garantir que REQUEST_URI e PATH_INFO tenham a barra inicial
// O problema pode estar no processamento interno do Laravel
$serverVars['REQUEST_URI'] = $laravelPath;
$serverVars['PATH_INFO'] = $laravelPath;

// Garantir que SCRIPT_NAME está correto
$serverVars['SCRIPT_NAME'] = '/index.php';
$serverVars['PHP_SELF'] = '/index.php';

// IMPORTANTE: O método Request::create() processa o primeiro parâmetro como URI
// Mas internamente, o Laravel pode estar removendo a barra inicial do path()
// Vamos garantir que o caminho esteja correto em todos os lugares

// Criar requisição explicitamente
// O primeiro parâmetro deve ser a URI completa (com ou sem query string)
$request = \Illuminate\Http\Request::create(
    $laravelPath,    // URI: /api/v1/login (caminho completo, SEMPRE começando com /)
    $method,         // POST
    $query,          // Query parameters
    $_COOKIE ?? [],  // Cookies
    $_FILES ?? [],   // Files
    $serverVars,     // Server vars (com headers corretos e caminhos ajustados)
    $body            // Request body
);

// DEBUG: Verificar se o caminho está sendo processado corretamente
// Se o path() retornar sem barra inicial, podemos tentar forçar via serverVars
$actualPath = $request->path();
if (substr($actualPath, 0, 1) !== '/' && substr($actualPath, 0, 4) !== 'api/') {
    // Se o path não começar com / nem com api/, pode haver problema
    file_put_contents($logFile, "WARNING: Request path() doesn't start with / or api/: $actualPath\n", FILE_APPEND);
}

// Log adicional para debug
file_put_contents($logFile, "Request path(): " . $request->path() . "\n", FILE_APPEND);
file_put_contents($logFile, "Request is('api/*'): " . ($request->is('api/*') ? 'yes' : 'no') . "\n", FILE_APPEND);
file_put_contents($logFile, "Request uri(): " . $request->getRequestUri() . "\n", FILE_APPEND);
file_put_contents($logFile, "Request getPathInfo(): " . $request->getPathInfo() . "\n", FILE_APPEND);
file_put_contents($logFile, "Request getRequestUri(): " . $request->getRequestUri() . "\n", FILE_APPEND);

// IMPORTANTE: O método path() do Laravel remove a barra inicial
// Mas o roteador precisa do caminho completo. Vamos verificar se o caminho está sendo processado corretamente
$requestPath = $request->path();
if ($requestPath === 'api/v1/login' || $requestPath === '/api/v1/login') {
    file_put_contents($logFile, "Path looks correct for routing\n", FILE_APPEND);
} else {
    file_put_contents($logFile, "WARNING: Path may not match routes: $requestPath\n", FILE_APPEND);
}

// Processar requisição - o Laravel envia a resposta automaticamente
try {
    // IMPORTANTE: O Laravel processa rotas baseado no caminho completo
    // Vamos garantir que o caminho está sendo passado corretamente
    // O método handleRequest() usa o caminho da requisição para encontrar a rota
    
    $response = $app->handleRequest($request);
    
    // Se houver resposta, enviar (pode já ter sido enviada)
    if ($response && !headers_sent()) {
        $response->send();
    }
} catch (\Throwable $e) {
    // Se houver erro, logar e retornar JSON de erro
    $errorMessage = $e->getMessage();
    $errorTrace = $e->getTraceAsString();
    
    file_put_contents($logFile, "ERROR: $errorMessage\n", FILE_APPEND);
    file_put_contents($logFile, "TRACE: $errorTrace\n", FILE_APPEND);
    
    // Se o erro for 404 (rota não encontrada), adicionar informações de debug
    if (strpos($errorMessage, 'could not be found') !== false) {
        file_put_contents($logFile, "DEBUG: Route not found. Request path: " . $request->path() . "\n", FILE_APPEND);
        file_put_contents($logFile, "DEBUG: Request URI: " . $request->getRequestUri() . "\n", FILE_APPEND);
        file_put_contents($logFile, "DEBUG: Path Info: " . $request->getPathInfo() . "\n", FILE_APPEND);
    }
    
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao processar requisição',
            'error' => $errorMessage
        ]);
    }
}
exit;
