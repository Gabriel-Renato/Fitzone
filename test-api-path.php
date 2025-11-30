<?php
/**
 * Script de teste para verificar como o PATH_INFO está sendo processado
 * Acesse: https://fitzone.wuaze.com/test-api-path.php?path=/api/index.php/v1/login
 */

header('Content-Type: application/json; charset=utf-8');

$testPath = $_GET['path'] ?? '/api/index.php/v1/login';

// Simular diferentes cenários
$scenarios = [
    'REQUEST_URI' => $testPath,
    'PATH_INFO' => $_SERVER['PATH_INFO'] ?? 'N/A',
];

// Testar extração
$results = [];

// Método 1: PATH_INFO
if (!empty($_SERVER['PATH_INFO'])) {
    $results['method1_path_info'] = '/api' . $_SERVER['PATH_INFO'];
}

// Método 2: Extrair da REQUEST_URI
if (preg_match('#^/api/index\.php(/.*)$#', $testPath, $matches)) {
    $results['method2_regex'] = '/api' . $matches[1];
}

// Método 3: str_replace simples
$results['method3_replace'] = str_replace('/api/index.php', '/api', $testPath);

echo json_encode([
    'input' => $testPath,
    'server_vars' => $scenarios,
    'extraction_results' => $results,
    'recommended_path' => $results['method2_regex'] ?? $results['method3_replace'] ?? 'N/A'
], JSON_PRETTY_PRINT);

