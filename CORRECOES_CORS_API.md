# 🔧 Correções CORS e URL da API

## ✅ Problemas Corrigidos:

### 1. **URL da API atualizada em todos os arquivos JS**

**Antes:**
```javascript
window.API_URL = 'https://laravel-backend-production-a6ef.up.railway.app/api/v1';
```

**Depois:**
```javascript
window.API_URL = 'https://fitzone.wuaze.com/api/v1';
```

**Arquivos atualizados:**
- ✅ `frontend/js/auth.js`
- ✅ `frontend/js/app.js`
- ✅ `frontend/js/dashboard-cliente.js`
- ✅ `frontend/js/dashboard-personal.js`

### 2. **CORS configurado no backend**

**Arquivo:** `backend/config/cors.php`

**Adicionado:**
```php
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:5173',
    'https://fitzone-frontend.vercel.app',
    'https://fitzone-frontend-git-main-gabrielrenatosouzadearaujo-9864.vercel.app',
    'https://fitzone.wuaze.com',  // ✅ ADICIONADO
    'http://fitzone.wuaze.com',   // ✅ ADICIONADO
],
```

## 📋 Próximos Passos:

### 1. **Configurar o backend para rodar no mesmo domínio**

O backend Laravel precisa estar acessível em `https://fitzone.wuaze.com/api/v1`.

**Opções:**

#### Opção A: Backend no mesmo servidor (Recomendado)
Se o backend está no mesmo servidor que o frontend:

1. Configure o Apache/Nginx para servir o Laravel:
   - DocumentRoot do Laravel: `/var/www/html/Fitzone/backend/public`
   - Ou configure um subdomínio/virtual host

2. Estrutura esperada:
   ```
   fitzone.wuaze.com/          → Frontend (htdocs/)
   fitzone.wuaze.com/api/v1/   → Backend Laravel (backend/public/)
   ```

#### Opção B: Backend em subdomínio
Se preferir separar:

1. Configure um subdomínio: `api.fitzone.wuaze.com`
2. Atualize os arquivos JS para usar: `https://api.fitzone.wuaze.com/api/v1`

### 2. **Limpar cache do Laravel**

Após fazer as alterações, limpe o cache:

```bash
cd /var/www/html/Fitzone/backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 3. **Verificar se o middleware CORS está ativo**

O Laravel já tem o middleware `HandleCors` ativo por padrão. Verifique se está funcionando testando uma requisição:

```bash
curl -H "Origin: https://fitzone.wuaze.com" \
     -H "Access-Control-Request-Method: GET" \
     -H "Access-Control-Request-Headers: X-Requested-With" \
     -X OPTIONS \
     https://fitzone.wuaze.com/api/v1/exercises
```

Deve retornar headers CORS permitindo a origem.

## 🐛 Troubleshooting

### Erro: "No 'Access-Control-Allow-Origin' header"

**Solução:**
1. Verifique se o domínio está em `allowed_origins` no `cors.php`
2. Limpe o cache: `php artisan config:clear`
3. Verifique se o middleware HandleCors está ativo
4. Verifique se o backend está acessível no mesmo domínio

### Erro: "Failed to fetch"

**Solução:**
1. Verifique se a URL da API está correta nos arquivos JS
2. Verifique se o backend está rodando e acessível
3. Verifique se as rotas da API estão configuradas corretamente

### Backend não responde

**Solução:**
1. Verifique se o Laravel está configurado corretamente
2. Verifique os logs: `backend/storage/logs/laravel.log`
3. Verifique se o `.env` está configurado corretamente
4. Teste: `php artisan route:list` para ver as rotas

## ✅ Checklist Final:

- [x] URLs da API atualizadas em todos os arquivos JS
- [x] CORS configurado para permitir `fitzone.wuaze.com`
- [ ] Backend configurado para rodar no mesmo domínio
- [ ] Cache do Laravel limpo
- [ ] Teste de requisição CORS funcionando
- [ ] Frontend fazendo requisições para a URL correta

