<?php
/**
 * Verificação simples de erros
 */

// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico do Servidor</h1>";

echo "<h2>1. PHP</h2>";
echo "Versão: " . PHP_VERSION . "<br>";

echo "<h2>2. Arquivos</h2>";
$files = [
    '../.env',
    '../vendor/autoload.php',
    '../bootstrap/app.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    echo $file . ": " . ($exists ? "✅ Existe" : "❌ Não existe") . "<br>";
    if ($exists) {
        echo "&nbsp;&nbsp;Path: " . realpath($path) . "<br>";
        echo "&nbsp;&nbsp;Readable: " . (is_readable($path) ? "✅" : "❌") . "<br>";
    }
}

echo "<h2>3. .env</h2>";
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    echo "✅ Arquivo existe<br>";
    echo "APP_ENV: " . (preg_match('/APP_ENV\s*=\s*(\w+)/', $content, $m) ? $m[1] : 'NÃO ENCONTRADO') . "<br>";
    echo "APP_DEBUG: " . (preg_match('/APP_DEBUG\s*=\s*(\w+)/', $content, $m) ? $m[1] : 'NÃO ENCONTRADO') . "<br>";
    echo "APP_KEY: " . (strpos($content, 'APP_KEY=base64:') !== false ? "✅ Configurado" : "❌ Não configurado") . "<br>";
} else {
    echo "❌ Arquivo .env NÃO existe!<br>";
}

echo "<h2>4. Tentando carregar Laravel</h2>";
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "✅ Autoload carregado<br>";
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✅ Laravel bootstrap OK<br>";
    echo "Versão: " . $app->version() . "<br>";
    
    $router = $app->make('router');
    $routes = $router->getRoutes();
    echo "✅ Rotas carregadas: " . count($routes) . "<br>";
    
} catch (\Throwable $e) {
    echo "❌ ERRO: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>5. Permissões</h2>";
$dirs = ['../storage', '../storage/logs', '../bootstrap/cache'];
foreach ($dirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    echo $dir . ": ";
    if (file_exists($path)) {
        echo (is_writable($path) ? "✅ Writable" : "❌ Not writable") . "<br>";
    } else {
        echo "❌ Não existe<br>";
    }
}
