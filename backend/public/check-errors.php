<?php
/**
 * Script de diagnóstico para verificar erros
 * Acesse: https://fitzone.wuaze.com/backend/public/check-errors.php
 */

// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header('Content-Type: application/json');

$results = [
    'php_version' => PHP_VERSION,
    'php_errors' => [],
    'file_permissions' => [],
    'env_status' => [],
    'laravel_status' => [],
];

// Verificar arquivos importantes
$importantFiles = [
    '../.env',
    '../vendor/autoload.php',
    '../bootstrap/app.php',
    '../routes/api.php',
];

foreach ($importantFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    $results['file_permissions'][$file] = [
        'exists' => file_exists($fullPath),
        'readable' => is_readable($fullPath),
        'writable' => is_writable($fullPath),
        'path' => realpath($fullPath) ?: 'NOT FOUND',
    ];
}

// Verificar .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $results['env_status'] = [
        'exists' => true,
        'readable' => true,
        'has_app_key' => strpos($envContent, 'APP_KEY=base64:') !== false,
        'app_env' => preg_match('/APP_ENV\s*=\s*(\w+)/', $envContent, $matches) ? $matches[1] : 'NOT FOUND',
        'app_debug' => preg_match('/APP_DEBUG\s*=\s*(\w+)/', $envContent, $matches) ? $matches[1] : 'NOT FOUND',
    ];
} else {
    $results['env_status'] = ['exists' => false];
}

// Tentar carregar Laravel
try {
    require __DIR__.'/../vendor/autoload.php';
    $results['laravel_status']['autoload'] = 'OK';
} catch (\Exception $e) {
    $results['laravel_status']['autoload'] = 'ERROR: ' . $e->getMessage();
}

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $results['laravel_status']['bootstrap'] = 'OK';
    $results['laravel_status']['version'] = $app->version();
} catch (\Exception $e) {
    $results['laravel_status']['bootstrap'] = 'ERROR: ' . $e->getMessage();
    $results['laravel_status']['error_trace'] = $e->getTraceAsString();
}

// Verificar permissões de diretórios
$importantDirs = [
    '../storage',
    '../storage/logs',
    '../storage/framework',
    '../storage/framework/cache',
    '../bootstrap/cache',
];

foreach ($importantDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    $results['file_permissions'][$dir] = [
        'exists' => file_exists($fullPath),
        'readable' => is_readable($fullPath),
        'writable' => is_writable($fullPath),
        'is_dir' => is_dir($fullPath),
    ];
}

// Verificar últimos erros do PHP
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $lastErrors = array_slice(file($errorLog), -10);
    $results['php_errors']['log_file'] = $errorLog;
    $results['php_errors']['last_errors'] = $lastErrors;
}

echo json_encode($results, JSON_PRETTY_PRINT);
