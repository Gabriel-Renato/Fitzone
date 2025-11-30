<?php
/**
 * Visualizador de Logs - Acesse: https://fitzone.wuaze.com/backend/view-logs.php
 */

header('Content-Type: text/html; charset=utf-8');

$logFile = __DIR__ . '/storage/logs/api-debug.log';
$laravelLog = __DIR__ . '/storage/logs/laravel.log';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Visualizador de Logs - FitZone</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .container { max-width: 1200px; margin: 0 auto; }
        .log-section { background: #252526; padding: 15px; margin: 20px 0; border-radius: 5px; border: 1px solid #3e3e42; }
        h1, h2 { color: #4ec9b0; }
        pre { background: #1e1e1e; padding: 15px; border-radius: 3px; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word; max-height: 600px; overflow-y: auto; }
        .timestamp { color: #569cd6; }
        .error { color: #f48771; }
        .success { color: #4ec9b0; }
        .info { color: #9cdcfe; }
        .refresh-btn { background: #007acc; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 0; }
        .refresh-btn:hover { background: #005a9e; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Visualizador de Logs - FitZone</h1>
        
        <button class="refresh-btn" onclick="location.reload()">🔄 Atualizar</button>
        
        <div class="log-section">
            <h2>🔵 API Debug Log (api-debug.log)</h2>
            <?php
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                $lines = explode("\n", $content);
                $recent = array_slice($lines, -100); // Últimas 100 linhas
                echo '<pre>' . htmlspecialchars(implode("\n", $recent)) . '</pre>';
                echo '<p class="info">Total de linhas: ' . count($lines) . ' | Mostrando últimas 100</p>';
            } else {
                echo '<p class="error">❌ Arquivo não encontrado: ' . htmlspecialchars($logFile) . '</p>';
                echo '<p class="info">Verifique se o arquivo existe e tem permissões de escrita.</p>';
            }
            ?>
        </div>
        
        <div class="log-section">
            <h2>📝 Laravel Log (laravel.log)</h2>
            <?php
            if (file_exists($laravelLog)) {
                $content = file_get_contents($laravelLog);
                $lines = explode("\n", $content);
                $recent = array_slice($lines, -200); // Últimas 200 linhas
                echo '<pre>' . htmlspecialchars(implode("\n", $recent)) . '</pre>';
                echo '<p class="info">Total de linhas: ' . count($lines) . ' | Mostrando últimas 200</p>';
            } else {
                echo '<p class="error">❌ Arquivo não encontrado: ' . htmlspecialchars($laravelLog) . '</p>';
            }
            ?>
        </div>
        
        <div class="log-section">
            <h2>📊 Informações do Sistema</h2>
            <pre>
Arquivo de Log API: <?php echo htmlspecialchars($logFile); ?>
Existe: <?php echo file_exists($logFile) ? '✅ SIM' : '❌ NÃO'; ?>
Legível: <?php echo is_readable($logFile) ? '✅ SIM' : '❌ NÃO'; ?>
Tamanho: <?php echo file_exists($logFile) ? filesize($logFile) . ' bytes' : 'N/A'; ?>

Arquivo de Log Laravel: <?php echo htmlspecialchars($laravelLog); ?>
Existe: <?php echo file_exists($laravelLog) ? '✅ SIM' : '❌ NÃO'; ?>
Legível: <?php echo is_readable($laravelLog) ? '✅ SIM' : '❌ NÃO'; ?>
Tamanho: <?php echo file_exists($laravelLog) ? filesize($laravelLog) . ' bytes' : 'N/A'; ?>

Diretório de Logs: <?php echo htmlspecialchars(__DIR__ . '/storage/logs'); ?>
Existe: <?php echo is_dir(__DIR__ . '/storage/logs') ? '✅ SIM' : '❌ NÃO'; ?>
Gravável: <?php echo is_writable(__DIR__ . '/storage/logs') ? '✅ SIM' : '❌ NÃO'; ?>
            </pre>
        </div>
    </div>
    
    <script>
        // Auto-refresh a cada 5 segundos
        setTimeout(() => location.reload(), 5000);
    </script>
</body>
</html>

