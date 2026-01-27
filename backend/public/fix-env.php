<?php
/**
 * Script para corrigir o .env e limpar cache
 * Acesse: https://fitzone.wuaze.com/backend/public/fix-env.php
 */

header('Content-Type: application/json');

$envFile = __DIR__ . '/../.env';
$result = ['success' => false, 'messages' => []];

if (!file_exists($envFile)) {
    echo json_encode(['success' => false, 'error' => 'Arquivo .env não encontrado'], JSON_PRETTY_PRINT);
    exit;
}

// Ler conteúdo atual
$envContent = file_get_contents($envFile);
$lines = explode("\n", $envContent);
$newLines = [];
$changes = [];

foreach ($lines as $line) {
    $originalLine = $line;
    
    // Corrigir APP_ENV
    if (preg_match('/^APP_ENV\s*=\s*.*$/', $line)) {
        $line = 'APP_ENV=production';
        $changes[] = 'APP_ENV alterado para production';
    }
    
    // Corrigir APP_DEBUG
    if (preg_match('/^APP_DEBUG\s*=\s*.*$/', $line)) {
        $line = 'APP_DEBUG=false';
        $changes[] = 'APP_DEBUG alterado para false';
    }
    
    // Corrigir APP_URL (remover barra final e mudar para https)
    if (preg_match('/^APP_URL\s*=\s*.*$/', $line)) {
        $line = 'APP_URL=https://fitzone.wuaze.com';
        $changes[] = 'APP_URL atualizado para https://fitzone.wuaze.com (sem barra)';
    }
    
    // Corrigir SANCTUM_STATEFUL_DOMAINS (adicionar domínio de produção)
    if (preg_match('/^SANCTUM_STATEFUL_DOMAINS\s*=\s*.*$/', $line)) {
        $line = 'SANCTUM_STATEFUL_DOMAINS=fitzone.wuaze.com,localhost,127.0.0.1,localhost:3000,localhost:5173';
        $changes[] = 'SANCTUM_STATEFUL_DOMAINS atualizado com domínio de produção';
    }
    
    // Corrigir SESSION_DRIVER (usar file em vez de database para evitar problemas)
    if (preg_match('/^SESSION_DRIVER\s*=\s*.*$/', $line)) {
        $line = 'SESSION_DRIVER=file';
        $changes[] = 'SESSION_DRIVER alterado para file';
    }
    
    // Corrigir CACHE_DRIVER (garantir que seja file)
    if (preg_match('/^CACHE_DRIVER\s*=\s*.*$/', $line)) {
        $line = 'CACHE_DRIVER=file';
        $changes[] = 'CACHE_DRIVER definido como file';
    }
    
    $newLines[] = $line;
}

// Escrever arquivo corrigido
$newContent = implode("\n", $newLines);
file_put_contents($envFile, $newContent);

$result['success'] = true;
$result['messages'] = $changes;
$result['message'] = 'Arquivo .env corrigido com sucesso!';

// Tentar limpar cache via artisan
chdir(__DIR__ . '/../');
$output = [];
$returnVar = 0;

// Limpar cache de configuração
exec('php artisan config:clear 2>&1', $output, $returnVar);
$result['config_clear'] = ['output' => $output, 'return' => $returnVar];

// Limpar cache de rotas
$output = [];
exec('php artisan route:clear 2>&1', $output, $returnVar);
$result['route_clear'] = ['output' => $output, 'return' => $returnVar];

// Limpar cache geral
$output = [];
exec('php artisan cache:clear 2>&1', $output, $returnVar);
$result['cache_clear'] = ['output' => $output, 'return' => $returnVar];

echo json_encode($result, JSON_PRETTY_PRINT);
