<?php

// Arquivo de entrada para API no Vercel
// Este arquivo redireciona todas as requisições para o Laravel

// Define o diretório raiz do Laravel
$laravelRoot = __DIR__ . '/..';

// Define o arquivo de entrada do Laravel
$laravelEntry = $laravelRoot . '/public/index.php';

// Verifica se o arquivo existe
if (file_exists($laravelEntry)) {
    // Define variáveis de ambiente para produção
    $_ENV['APP_ENV'] = 'production';
    $_ENV['APP_DEBUG'] = 'false';
    
    // Inclui o arquivo de entrada do Laravel
    require $laravelEntry;
} else {
    // Retorna erro se o Laravel não estiver configurado
    http_response_code(500);
    echo json_encode([
        'error' => 'Laravel application not found',
        'message' => 'Please ensure Laravel is properly installed'
    ]);
}
