<?php
/**
 * Arquivo de teste simples na raiz
 */
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Arquivo test-api.php funciona!',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'não definido',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'não definido',
], JSON_PRETTY_PRINT);

