# 🚀 Guia de Deploy do FitZone Backend no Vercel

## 📋 Pré-requisitos

1. Conta no [Vercel](https://vercel.com)
2. Conta no [PlanetScale](https://planetscale.com) ou outro provedor de banco MySQL
3. Git configurado
4. Node.js e Composer instalados

## 🔧 Configuração Passo a Passo

### 1. Preparar o Repositório

```bash
# Navegue até a pasta do backend
cd backend

# Instale as dependências
composer install

# Copie o arquivo de configuração
cp env.vercel.example .env

# Gere uma chave de aplicação
php artisan key:generate
```

### 2. Configurar Banco de Dados

#### Opção A: PlanetScale (Recomendado)
1. Crie uma conta no [PlanetScale](https://planetscale.com)
2. Crie um novo banco de dados
3. Obtenha a string de conexão
4. Configure as variáveis de ambiente no Vercel

#### Opção B: Railway
1. Crie uma conta no [Railway](https://railway.app)
2. Crie um serviço MySQL
3. Obtenha as credenciais de conexão

### 3. Configurar Variáveis de Ambiente no Vercel

No dashboard do Vercel, vá em **Settings > Environment Variables** e adicione:

```env
APP_NAME=FitZone
APP_ENV=production
APP_KEY=sua-chave-aqui
APP_DEBUG=false
APP_URL=https://seu-app.vercel.app

DB_CONNECTION=mysql
DB_HOST=seu-host
DB_PORT=3306
DB_DATABASE=nome-do-banco
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha

SANCTUM_STATEFUL_DOMAINS=seu-frontend-domain.vercel.app
```

### 4. Deploy no Vercel

#### Método 1: Via CLI do Vercel
```bash
# Instale o Vercel CLI
npm i -g vercel

# Faça login
vercel login

# Deploy
vercel --prod
```

#### Método 2: Via GitHub
1. Conecte seu repositório ao Vercel
2. Configure o **Root Directory** como `backend`
3. Configure o **Build Command** como `npm run vercel-build`
4. Configure o **Output Directory** como `public`
5. Faça o deploy

### 5. Executar Migrations

Após o deploy, execute as migrations:

```bash
# Via Vercel CLI
vercel env pull .env.local
php artisan migrate --force

# Ou configure um webhook para executar automaticamente
```

### 6. Configurar Domínio Personalizado (Opcional)

1. No dashboard do Vercel, vá em **Settings > Domains**
2. Adicione seu domínio personalizado
3. Configure os registros DNS conforme instruído

## 🔍 Verificação do Deploy

### Teste a API:
```bash
# Teste a rota de health check
curl https://seu-app.vercel.app/up

# Teste uma rota da API
curl https://seu-app.vercel.app/api/v1/exercises
```

### Logs:
- Acesse o dashboard do Vercel
- Vá em **Functions** para ver os logs
- Monitore erros e performance

## 🛠️ Troubleshooting

### Erro de Banco de Dados:
- Verifique se as variáveis de ambiente estão corretas
- Confirme se o banco está acessível
- Verifique se as migrations foram executadas

### Erro de CORS:
- Atualize o arquivo `config/cors.php`
- Adicione o domínio do frontend nas origens permitidas

### Erro de Storage:
- O Vercel usa sistema de arquivos temporário
- Para uploads, considere usar AWS S3 ou similar

## 📚 Recursos Úteis

- [Documentação do Vercel](https://vercel.com/docs)
- [Laravel no Vercel](https://vercel.com/guides/deploying-laravel-with-vercel)
- [PlanetScale Docs](https://planetscale.com/docs)

## 🎯 Próximos Passos

1. Configure CI/CD para deploys automáticos
2. Configure monitoramento e alertas
3. Implemente cache Redis (opcional)
4. Configure backup automático do banco
