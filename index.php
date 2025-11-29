<?php
/**
 * FitZone - Index Principal
 * Detecta se é requisição da API e redireciona, senão serve o frontend
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Remover query string da URI
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Se for requisição da API, redirecionar para api.php
if (strpos($requestPath, '/api/') === 0) {
    require __DIR__ . '/api.php';
    exit;
}

// Verificar se é um arquivo estático que existe fisicamente
$staticFile = __DIR__ . $requestPath;

// Se o arquivo não existe e não tem extensão, tentar extensões comuns
if (!file_exists($staticFile) && !pathinfo($requestPath, PATHINFO_EXTENSION)) {
    $commonExtensions = ['css', 'js', 'html', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'json'];
    foreach ($commonExtensions as $ext) {
        $tryFile = $staticFile . '.' . $ext;
        if (file_exists($tryFile) && is_file($tryFile)) {
            $staticFile = $tryFile;
            break;
        }
    }
}

if (file_exists($staticFile) && is_file($staticFile)) {
    // Determinar content-type
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $contentTypes = [
        'html' => 'text/html; charset=utf-8',
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    
    if (isset($contentTypes[$ext])) {
        header('Content-Type: ' . $contentTypes[$ext]);
    }
    
    // Cache para arquivos estáticos
    if (in_array($ext, ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2'])) {
        header('Cache-Control: public, max-age=31536000');
    }
    
    readfile($staticFile);
    exit;
}

// Se for a raiz ou index, servir frontend/index.html
if ($requestPath === '/' || $requestPath === '/index.php' || $requestPath === '/index.html') {
    $frontendIndex = __DIR__ . '/frontend/index.html';
    if (file_exists($frontendIndex)) {
        header('Content-Type: text/html; charset=utf-8');
        readfile($frontendIndex);
        exit;
    }
}

// Tentar servir arquivo do frontend
$frontendFile = __DIR__ . '/frontend' . $requestPath;

// Se o arquivo não existe e não tem extensão, tentar extensões comuns
// Priorizar CSS e JS baseado no caminho
if (!file_exists($frontendFile) || !is_file($frontendFile)) {
    $hasExtension = pathinfo($requestPath, PATHINFO_EXTENSION);
    
    if (!$hasExtension) {
        // Detectar tipo pelo caminho
        $pathLower = strtolower($requestPath);
        $extensions = [];
        
        if (strpos($pathLower, '/css/') !== false || strpos($pathLower, 'css') !== false) {
            $extensions = ['css'];
        } elseif (strpos($pathLower, '/js/') !== false || strpos($pathLower, 'js') !== false) {
            $extensions = ['js'];
        } else {
            $extensions = ['css', 'js', 'html', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'json'];
        }
        
        foreach ($extensions as $ext) {
            $tryFile = $frontendFile . '.' . $ext;
            if (file_exists($tryFile) && is_file($tryFile)) {
                $frontendFile = $tryFile;
                break;
            }
        }
    }
}

if (file_exists($frontendFile) && is_file($frontendFile)) {
    // Determinar content-type
    $ext = strtolower(pathinfo($frontendFile, PATHINFO_EXTENSION));
    $contentTypes = [
        'html' => 'text/html; charset=utf-8',
        'css' => 'text/css; charset=utf-8',
        'js' => 'application/javascript; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'svg' => 'image/svg+xml',
    ];
    
    if (isset($contentTypes[$ext])) {
        header('Content-Type: ' . $contentTypes[$ext]);
    }
    
    readfile($frontendFile);
    exit;
}

// Se chegou aqui, arquivo não encontrado
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 - Arquivo não encontrado</h1><p>URI: ' . htmlspecialchars($requestPath) . '</p></body></html>';

