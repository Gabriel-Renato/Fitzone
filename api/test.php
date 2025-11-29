<?php
/**
 * Arquivo de teste para verificar o redirecionamento
 */

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Arquivo api/test.php está funcionando',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'não definido',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'não definido',
    'php_self' => $_SERVER['PHP_SELF'] ?? 'não definido',
    'laravel_path' => __DIR__ . '/../backend/public/index.php',
    'laravel_exists' => file_exists(__DIR__ . '/../backend/public/index.php'),
], JSON_PRETTY_PRINT);

