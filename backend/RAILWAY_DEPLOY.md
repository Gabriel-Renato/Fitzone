# 🚂 Deploy do FitZone Backend no Railway

## 🎯 Por que Railway?

- ✅ **Suporte nativo** para PHP/Laravel
- ✅ **Deploy automático** via Git
- ✅ **Banco MySQL** incluído
- ✅ **Gratuito** até 5GB
- ✅ **SSL** automático
- ✅ **Domínio** personalizado

## 🚀 Deploy Passo a Passo

### 1. Instalar Railway CLI

```bash
npm install -g @railway/cli
```

### 2. Fazer Login

```bash
railway login
```

### 3. Inicializar Projeto

```bash
# Na pasta do backend
cd backend
railway init
```

### 4. Configurar Banco de Dados

```bash
# Adicionar serviço MySQL
railway add mysql
```

### 5. Configurar Variáveis de Ambiente

```bash
# Configurar variáveis
railway variables set APP_NAME="FitZone"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_URL=https://seu-app.railway.app

# Gerar chave da aplicação
railway variables set APP_KEY=$(php artisan key:generate --show)
```

### 6. Deploy

```bash
railway up
```

### 7. Executar Migrations

```bash
railway run php artisan migrate --force
```

## 🔧 Configuração Manual (Alternativa)

### 1. Acesse [railway.app](https://railway.app)
### 2. Conecte seu GitHub
### 3. Selecione o repositório `Fitzone`
### 4. Configure o **Root Directory** como `backend`
### 5. Adicione serviço MySQL
### 6. Configure as variáveis de ambiente

## 📋 Variáveis de Ambiente Necessárias

```env
APP_NAME=FitZone
APP_ENV=production
APP_KEY=base64:sua-chave-aqui
APP_DEBUG=false
APP_URL=https://seu-app.railway.app

DB_CONNECTION=mysql
DB_HOST=containers-us-west-xxx.railway.app
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=sua-senha-aqui

SANCTUM_STATEFUL_DOMAINS=seu-frontend-domain.vercel.app
```

## 🎯 Configuração do Frontend

Após o deploy do backend, configure o frontend no Vercel para apontar para:

```javascript
// No seu frontend
const API_BASE_URL = 'https://seu-app.railway.app/api/v1';
```

## 🔍 Verificação do Deploy

### Teste a API:
```bash
# Health check
curl https://seu-app.railway.app/up

# Teste da API
curl https://seu-app.railway.app/api/v1/exercises
```

## 🆘 Troubleshooting

### Erro de Banco:
- Verifique se o serviço MySQL foi adicionado
- Confirme as variáveis de ambiente

### Erro de Migrations:
- Execute: `railway run php artisan migrate --force`

### Erro de Permissões:
- Verifique se o usuário tem acesso ao repositório

## 💡 Dicas Importantes

1. **Railway é gratuito** até 5GB
2. **Deploy automático** a cada push
3. **Logs** disponíveis no dashboard
4. **Domínio** personalizado disponível
5. **Escalável** conforme necessário

## 🎉 Próximo Passo

Após o deploy no Railway, configure o frontend no Vercel para se comunicar com o backend!
