# 🔍 Como Descobrir o DB_HOST do InfinityFree

## ⚠️ Problema Atual:

O `.env` está com `DB_HOST=sqlXXX.infinityfree.com` (placeholder). Você precisa descobrir o hostname real do MySQL.

## 📋 Métodos para Descobrir:

### Método 1: Painel InfinityFree (Recomendado)

1. Acesse o painel do InfinityFree: https://infinityfree.net/
2. Faça login na sua conta
3. Vá em **"MySQL Databases"** ou **"Databases"**
4. Procure por informações da conexão MySQL
5. Procure por campos como:
   - **"Host"**
   - **"Server"**
   - **"MySQL Host"**
   - **"Database Host"**

O hostname geralmente é algo como:
- `sqlXXX.infinityfree.com` (onde XXX é um número)
- Ou um IP direto

### Método 2: Via phpMyAdmin

1. Acesse o phpMyAdmin do InfinityFree
2. Na página inicial, procure por **"Server"** ou **"Host"**
3. O hostname do MySQL geralmente aparece lá

### Método 3: Testar Hosts Comuns

InfinityFree geralmente usa padrões como:
- `sqlXXX.infinityfree.com` (onde XXX varia: 100, 101, 102, etc.)
- Ou verifique no painel qual é o número do seu servidor

### Método 4: Verificar Variáveis de Ambiente

Se você tem acesso SSH ou pode executar PHP:

```php
<?php
// Criar arquivo test-db.php
$hosts = [
    'sql100.infinityfree.com',
    'sql101.infinityfree.com',
    'sql102.infinityfree.com',
    'sql103.infinityfree.com',
    // Adicione mais se necessário
];

foreach ($hosts as $host) {
    $connection = @mysqli_connect($host, 'if0_40475890', 'EsxdvH1MEcEqkK', 'if0_40475890_fitzone');
    if ($connection) {
        echo "✅ Host correto: $host\n";
        mysqli_close($connection);
        break;
    }
}
```

## ✅ Após Descobrir:

1. Edite o arquivo `.env`:
   ```bash
   nano /var/www/html/Fitzone/backend/.env
   ```

2. Atualize o `DB_HOST`:
   ```env
   DB_HOST=sqlXXX.infinityfree.com  # Substitua XXX pelo número correto
   ```

3. Limpe o cache:
   ```bash
   cd /var/www/html/Fitzone/backend
   php artisan config:clear
   php artisan cache:clear
   ```

4. Teste a conexão:
   ```bash
   php artisan migrate:status
   ```

## 🔧 Solução Temporária:

Enquanto não descobre o hostname correto, mudei o `SESSION_DRIVER` para `file` para evitar erros de sessão. Isso permite que o sistema funcione mesmo sem o banco configurado corretamente.

Para voltar a usar `database` depois:
```env
SESSION_DRIVER=database
```

## 📝 Nota:

O erro também mostra tentativas de conectar ao `mysql.railway.internal`, o que indica que pode haver cache antigo. Sempre limpe o cache após alterar o `.env`:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

