# 🚀 Guia Completo: PlanetScale + Vercel

## 📋 Passo a Passo Detalhado

### 1. Criar Banco no PlanetScale

1. **Acesse**: [planetscale.com](https://planetscale.com)
2. **Crie conta** gratuita
3. **Clique** em "Create database"
4. **Nome**: `fitzone-db` (ou qualquer nome)
5. **Região**: Escolha a mais próxima (ex: São Paulo)
6. **Clique** em "Create database"

### 2. Obter Credenciais de Conexão

1. **No dashboard** do PlanetScale, clique no seu banco
2. **Vá** em "Connect"
3. **Copie** a string de conexão (algo como):
   ```
   mysql://username:password@host:port/database?sslaccept=strict
   ```

### 3. Configurar no Vercel

1. **Acesse** o dashboard do Vercel
2. **Vá** em seu projeto > Settings > Environment Variables
3. **Adicione** estas variáveis:

```env
DB_CONNECTION=mysql
DB_HOST=aws.connect.psdb.cloud
DB_PORT=3306
DB_DATABASE=fitzone-db
DB_USERNAME=seu-usuario-aqui
DB_PASSWORD=sua-senha-aqui
DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt
```

### 4. Executar Migrations

Após o deploy, você precisa executar as migrations:

**Opção A: Via Vercel CLI**
```bash
# Instale o Vercel CLI
npm i -g vercel

# Faça login
vercel login

# Execute as migrations
vercel env pull .env.local
php artisan migrate --force
```

**Opção B: Via Dashboard PlanetScale**
1. Acesse o PlanetScale
2. Vá em "Console" 
3. Execute os comandos SQL das suas migrations

### 5. Testar a Conexão

```bash
# Teste se a API está funcionando
curl https://seu-app.vercel.app/api/v1/exercises
```

## 🔧 Configuração Alternativa (SQLite para Teste)

Se quiser testar rapidamente sem banco externo:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
```

⚠️ **Atenção**: SQLite no Vercel é temporário e os dados são perdidos a cada deploy.

## 🆘 Problemas Comuns

### Erro: "SSL connection error"
**Solução**: Adicione `DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt`

### Erro: "Database not found"
**Solução**: Verifique se o nome do banco está correto

### Erro: "Access denied"
**Solução**: Verifique usuário e senha

## 💡 Dicas Importantes

1. **PlanetScale é gratuito** até 1GB
2. **Backup automático** incluído
3. **Escalável** conforme necessário
4. **Interface web** para gerenciar dados
5. **Compatível 100%** com Laravel

## 🎯 Próximo Passo

Após configurar o banco, você pode fazer o deploy do backend no Vercel seguindo o `DEPLOY_GUIDE.md`!
