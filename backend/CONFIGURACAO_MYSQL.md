# 🔧 Configuração MySQL - FitZone

## ✅ O que foi configurado:

1. **Arquivo `.env` criado** com configurações MySQL
2. **`config/database.php`** atualizado para usar MySQL como padrão
3. **Chave da aplicação gerada** automaticamente

## 📝 Configurar Credenciais do Banco de Dados

Edite o arquivo `/var/www/html/Fitzone/backend/.env` e ajuste as seguintes variáveis:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1        # IP ou hostname do servidor MySQL
DB_PORT=3306             # Porta do MySQL (padrão: 3306)
DB_DATABASE=fitzone      # Nome do banco de dados
DB_USERNAME=root         # Usuário do MySQL
DB_PASSWORD=             # Senha do MySQL (deixe vazio se não tiver senha)
```

### Exemplos de Configuração:

#### MySQL Local (sem senha):
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitzone
DB_USERNAME=root
DB_PASSWORD=
```

#### MySQL Local (com senha):
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitzone
DB_USERNAME=root
DB_PASSWORD=suasenha123
```

#### MySQL Remoto:
```env
DB_HOST=185.27.134.128
DB_PORT=3306
DB_DATABASE=fitzone
DB_USERNAME=seu_usuario
DB_PASSWORD=suasenha123
```

## 🗄️ Criar o Banco de Dados

Antes de usar o sistema, você precisa:

1. **Criar o banco de dados MySQL:**
```sql
CREATE DATABASE fitzone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Importar a estrutura:**
```bash
mysql -u root -p fitzone < /var/www/html/Fitzone/database_completo.sql
```

Ou via phpMyAdmin/HeidiSQL:
- Selecione o banco `fitzone`
- Execute o conteúdo do arquivo `database_completo.sql`

## ✅ Verificar Conexão

Para testar se a conexão está funcionando:

```bash
cd /var/www/html/Fitzone/backend
php artisan migrate:status
```

Ou teste a conexão diretamente:

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

Se retornar um objeto PDO, a conexão está funcionando! ✅

## 🔄 Limpar Cache (se necessário)

Se você alterar as configurações do `.env`, limpe o cache:

```bash
cd /var/www/html/Fitzone/backend
php artisan config:clear
php artisan cache:clear
```

## 📋 Checklist

- [ ] Arquivo `.env` configurado com credenciais corretas
- [ ] Banco de dados `fitzone` criado
- [ ] Arquivo `database_completo.sql` importado
- [ ] Teste de conexão realizado com sucesso
- [ ] Cache limpo (se necessário)

## 🐛 Troubleshooting

### Erro: "Access denied for user"
- Verifique se o usuário e senha estão corretos no `.env`
- Verifique se o usuário tem permissão para acessar o banco

### Erro: "Unknown database 'fitzone'"
- Crie o banco de dados primeiro: `CREATE DATABASE fitzone;`
- Ou importe o `database_completo.sql` que cria automaticamente

### Erro: "could not find driver"
- Instale a extensão PHP MySQL:
  ```bash
  # Ubuntu/Debian
  sudo apt-get install php-mysql
  
  # Verificar se está instalado
  php -m | grep pdo_mysql
  ```

### Erro: "Connection refused"
- Verifique se o MySQL está rodando
- Verifique se o host e porta estão corretos
- Verifique firewall/iptables se for conexão remota

