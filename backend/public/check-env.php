<?php
/**
 * Script para verificar configuração do .env
 * Acesse: https://fitzone.wuaze.com/backend/public/check-env.php
 */

header('Content-Type: application/json');

$envFile = __DIR__ . '/../.env';
$envExists = file_exists($envFile);

$result = [
    'env_exists' => $envExists,
    'env_path' => realpath(__DIR__ . '/../') . '/.env',
    'current_dir' => __DIR__,
    'parent_dir' => realpath(__DIR__ . '/../'),
];

if ($envExists) {
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    
    $safeKeys = ['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'SESSION_DRIVER', 'CACHE_STORE', 'QUEUE_CONNECTION'];
    
    $envVars = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            if (in_array($key, $safeKeys)) {
                $envVars[$key] = trim($parts[1]);
            }
        }
    }
    
    $result['env_vars'] = $envVars;
    $result['has_app_key'] = strpos($envContent, 'APP_KEY=base64:') !== false;
} else {
    $result['message'] = 'Arquivo .env não encontrado! Você precisa fazer upload do .env para o servidor.';
    
    // Listar arquivos no diretório
    $files = scandir(__DIR__ . '/../');
    $result['files_in_backend'] = $files;
}

echo json_encode($result, JSON_PRETTY_PRINT);
