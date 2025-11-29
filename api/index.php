<?php
/**
 * FitZone - Redirecionador de API
 * Redireciona todas as requisições /api/* para o backend Laravel
 */

// Capturar a URI da requisição original
$originalUri = $_SERVER['REQUEST_URI'];

// Remover /api da URI para obter o caminho da API
$apiPath = preg_replace('#^/api#', '', $originalUri);

// Garantir que comece com /
if (substr($apiPath, 0, 1) !== '/') {
    $apiPath = '/' . $apiPath;
}

// Se não começar com /v1, adicionar
if (substr($apiPath, 0, 3) !== '/v1') {
    $apiPath = '/v1' . $apiPath;
}

// Caminho absoluto para o index.php do Laravel
$laravelPath = __DIR__ . '/../backend/public/index.php';

// Verificar se o arquivo existe
if (!file_exists($laravelPath)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Backend Laravel não encontrado em: ' . $laravelPath
    ]);
    exit;
}

// Ajustar variáveis de ambiente para o Laravel
$_SERVER['SCRIPT_NAME'] = '/api/index.php';
$_SERVER['REQUEST_URI'] = $apiPath;
$_SERVER['PHP_SELF'] = '/api/index.php';

// Manter o método HTTP original
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Incluir e executar o Laravel
chdir(dirname($laravelPath));
require $laravelPath;

