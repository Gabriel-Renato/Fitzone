<?php
/**
 * Script rápido para verificar conexão com banco de dados
 * Acesse: https://fitzone.wuaze.com/backend/check-database.php
 */

// Carregar .env
$envPath = __DIR__ . '/.env';
$config = [];

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $config[trim($key)] = trim($value, '"\'');
        }
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Conexão - Banco de Dados</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Teste de Conexão - Banco de Dados</h1>
    
    <div class="section">
        <h2>📋 Configuração do .env</h2>
        <pre><?php
            echo "DB_CONNECTION: " . ($config['DB_CONNECTION'] ?? 'NÃO DEFINIDO') . "\n";
            echo "DB_HOST: " . ($config['DB_HOST'] ?? 'NÃO DEFINIDO') . "\n";
            echo "DB_PORT: " . ($config['DB_PORT'] ?? 'NÃO DEFINIDO') . "\n";
            echo "DB_DATABASE: " . ($config['DB_DATABASE'] ?? 'NÃO DEFINIDO') . "\n";
            echo "DB_USERNAME: " . ($config['DB_USERNAME'] ?? 'NÃO DEFINIDO') . "\n";
            echo "DB_PASSWORD: " . (isset($config['DB_PASSWORD']) ? '***DEFINIDO***' : 'NÃO DEFINIDO') . "\n";
        ?></pre>
    </div>
    
    <div class="section">
        <h2>🔌 Extensões PHP</h2>
        <pre><?php
            echo "PDO: " . (extension_loaded('pdo') ? '✅ Carregado' : '❌ Não carregado') . "\n";
            echo "PDO_MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Carregado' : '❌ Não carregado') . "\n";
            echo "MySQLi: " . (extension_loaded('mysqli') ? '✅ Carregado' : '❌ Não carregado') . "\n";
        ?></pre>
    </div>
    
    <div class="section">
        <h2>🔗 Teste de Conexão</h2>
        <?php
        try {
            $host = $config['DB_HOST'] ?? 'localhost';
            $port = $config['DB_PORT'] ?? 3306;
            $database = $config['DB_DATABASE'] ?? '';
            $username = $config['DB_USERNAME'] ?? '';
            $password = $config['DB_PASSWORD'] ?? '';
            
            if (empty($database) || empty($username)) {
                throw new Exception('Configuração incompleta no .env');
            }
            
            echo "<p class='info'>Tentando conectar em: <strong>$host:$port</strong></p>";
            echo "<p class='info'>Database: <strong>$database</strong></p>";
            echo "<p class='info'>Usuário: <strong>$username</strong></p>";
            
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            echo "<p class='success'>✅ <strong>CONEXÃO ESTABELECIDA COM SUCESSO!</strong></p>";
            
            // Informações do MySQL
            $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as db");
            $info = $stmt->fetch();
            echo "<p class='success'>Versão MySQL: <strong>{$info['version']}</strong></p>";
            echo "<p class='success'>Database atual: <strong>{$info['db']}</strong></p>";
            
            // Listar tabelas
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<p class='success'>Tabelas encontradas: <strong>" . count($tables) . "</strong></p>";
            
            if (count($tables) > 0) {
                echo "<pre>";
                foreach ($tables as $table) {
                    echo "- $table\n";
                }
                echo "</pre>";
            }
            
            // Verificar tabelas importantes
            $importantTables = ['users', 'exercises', 'workouts', 'workout_plans'];
            echo "<h3>Tabelas Importantes:</h3><pre>";
            foreach ($importantTables as $table) {
                $exists = in_array($table, $tables);
                echo ($exists ? "✅" : "❌") . " $table\n";
            }
            echo "</pre>";
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ <strong>ERRO DE CONEXÃO PDO:</strong></p>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<p class='error'>Código: " . $e->getCode() . "</p>";
        } catch (Exception $e) {
            echo "<p class='error'>❌ <strong>ERRO:</strong></p>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>📝 Logs Recentes da API</h2>
        <?php
        $logFile = __DIR__ . '/storage/logs/api-debug.log';
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $lines = explode("\n", $logs);
            $recent = array_slice($lines, -50); // Últimas 50 linhas
            echo "<pre>" . htmlspecialchars(implode("\n", $recent)) . "</pre>";
        } else {
            echo "<p class='info'>Arquivo de log não encontrado ainda.</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>📝 Logs do Laravel</h2>
        <?php
        $laravelLog = __DIR__ . '/storage/logs/laravel.log';
        if (file_exists($laravelLog)) {
            $logs = file_get_contents($laravelLog);
            $lines = explode("\n", $logs);
            $recent = array_slice($lines, -100); // Últimas 100 linhas
            echo "<pre>" . htmlspecialchars(implode("\n", $recent)) . "</pre>";
        } else {
            echo "<p class='info'>Arquivo de log não encontrado.</p>";
        }
        ?>
    </div>
</body>
</html>

