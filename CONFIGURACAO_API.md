# 🔧 Configuração da API - InfinityFree

## ✅ Solução Implementada:

Criado um sistema de redirecionamento para que as requisições `/api/v1/*` sejam direcionadas para o backend Laravel.

### Estrutura Criada:

1. **`/api/index.php`** - Arquivo PHP que redireciona requisições para o Laravel
2. **`.htaccess`** na raiz - Configuração do Apache para roteamento

## 📋 Como Funciona:

1. Frontend faz requisição: `https://fitzone.wuaze.com/api/v1/exercises`
2. `.htaccess` captura e redireciona para `/api/index.php`
3. `api/index.php` processa e redireciona para `backend/public/index.php`
4. Laravel processa a requisição normalmente

## 🔍 Verificar se está funcionando:

### Teste 1: Verificar se o arquivo existe
```bash
ls -la /var/www/html/Fitzone/api/index.php
ls -la /var/www/html/Fitzone/.htaccess
```

### Teste 2: Testar requisição direta
```bash
curl https://fitzone.wuaze.com/api/v1/exercises
```

Deve retornar JSON ou erro do Laravel (não erro 404 do InfinityFree).

### Teste 3: Verificar logs do Laravel
```bash
tail -f /var/www/html/Fitzone/backend/storage/logs/laravel.log
```

## 🐛 Troubleshooting:

### Erro: "Backend Laravel não encontrado"

**Solução:**
1. Verifique se o caminho está correto:
   ```bash
   ls -la /var/www/html/Fitzone/backend/public/index.php
   ```

2. Ajuste o caminho no arquivo `api/index.php` se necessário

### Erro: 404 do InfinityFree

**Solução:**
1. Verifique se o `.htaccess` está na raiz do projeto
2. Verifique se o mod_rewrite está habilitado no Apache
3. Tente acessar diretamente: `https://fitzone.wuaze.com/api/index.php`

### Erro: CORS ainda bloqueando

**Solução:**
1. Verifique se o domínio está em `backend/config/cors.php`
2. Limpe o cache: `php artisan config:clear`
3. Verifique se o middleware HandleCors está ativo

### Rotas não encontradas

**Solução:**
1. Verifique se as rotas estão registradas:
   ```bash
   cd /var/www/html/Fitzone/backend
   php artisan route:list | grep api
   ```

2. Limpe o cache de rotas:
   ```bash
   php artisan route:clear
   php artisan config:clear
   ```

## 📝 Estrutura de Arquivos:

```
Fitzone/
├── .htaccess              ← Redireciona /api/* para api/index.php
├── api/
│   └── index.php         ← Redireciona para backend/public/index.php
├── backend/
│   ├── public/
│   │   └── index.php     ← Laravel entry point
│   └── routes/
│       └── api.php       ← Rotas da API
└── frontend/
    └── js/
        └── *.js         ← Frontend fazendo requisições para /api/v1/*
```

## ✅ Checklist:

- [x] Arquivo `api/index.php` criado
- [x] Arquivo `.htaccess` configurado
- [ ] Testar requisição: `curl https://fitzone.wuaze.com/api/v1/exercises`
- [ ] Verificar se retorna JSON (não erro 404)
- [ ] Verificar logs do Laravel para erros
- [ ] Testar no navegador se CORS está funcionando


