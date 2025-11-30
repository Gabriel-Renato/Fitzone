<?php
/**
 * Script de teste de conexão com o banco de dados
 * Acesse via: https://fitzone.wuaze.com/backend/test-db-connection.php
 */

// Carregar variáveis de ambiente
$envFile = __DIR__ . '/.env';
$env = [];

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
}

header('Content-Type: application/json; charset=utf-8');

$result = [
    'timestamp' => date('Y-m-d H:i:s'),
    'env_file_exists' => file_exists($envFile),
    'env_file_path' => $envFile,
    'database_config' => [
        'DB_CONNECTION' => $env['DB_CONNECTION'] ?? 'N/A',
        'DB_HOST' => $env['DB_HOST'] ?? 'N/A',
        'DB_PORT' => $env['DB_PORT'] ?? 'N/A',
        'DB_DATABASE' => $env['DB_DATABASE'] ?? 'N/A',
        'DB_USERNAME' => $env['DB_USERNAME'] ?? 'N/A',
        'DB_PASSWORD' => $env['DB_PASSWORD'] ? '***' : 'N/A',
    ],
    'php_extensions' => [
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'mysqli' => extension_loaded('mysqli'),
    ],
    'connection_test' => null,
    'errors' => []
];

// Testar conexão
try {
    $host = $env['DB_HOST'] ?? 'localhost';
    $port = $env['DB_PORT'] ?? 3306;
    $database = $env['DB_DATABASE'] ?? '';
    $username = $env['DB_USERNAME'] ?? '';
    $password = $env['DB_PASSWORD'] ?? '';
    
    if (empty($database) || empty($username)) {
        throw new Exception('Configuração de banco de dados incompleta');
    }
    
    // Tentar conexão PDO
    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Testar query
    $stmt = $pdo->query("SELECT 1 as test, DATABASE() as current_db, VERSION() as mysql_version");
    $testResult = $stmt->fetch();
    
    // Verificar tabelas
    $tablesStmt = $pdo->query("SHOW TABLES");
    $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
    
    $result['connection_test'] = [
        'status' => 'success',
        'mysql_version' => $testResult['mysql_version'] ?? 'N/A',
        'current_database' => $testResult['current_db'] ?? 'N/A',
        'tables_count' => count($tables),
        'tables' => $tables,
    ];
    
} catch (PDOException $e) {
    $result['connection_test'] = [
        'status' => 'error',
        'error_code' => $e->getCode(),
        'error_message' => $e->getMessage(),
    ];
    $result['errors'][] = 'PDO Error: ' . $e->getMessage();
} catch (Exception $e) {
    $result['connection_test'] = [
        'status' => 'error',
        'error_message' => $e->getMessage(),
    ];
    $result['errors'][] = 'Error: ' . $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

