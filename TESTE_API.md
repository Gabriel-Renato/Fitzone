# 🧪 Teste da API - FitZone

## ✅ Arquivos Criados:

1. **`.htaccess`** - Redireciona `/api/*` para `api/index.php`
2. **`api/index.php`** - Processa e redireciona para o Laravel
3. **`api/test.php`** - Arquivo de teste

## 🧪 Como Testar:

### Teste 1: Verificar se api/test.php funciona
Acesse no navegador:
```
https://fitzone.wuaze.com/api/test.php
```

Deve retornar JSON com informações sobre a requisição.

### Teste 2: Testar redirecionamento para Laravel
Acesse no navegador ou via curl:
```
https://fitzone.wuaze.com/api/v1/exercises
```

**Resultado esperado:**
- ✅ JSON com lista de exercícios (se autenticado)
- ✅ JSON com erro de autenticação (se não autenticado)
- ❌ HTML com erro 404 (se não funcionar)

### Teste 3: Verificar logs do Laravel
```bash
tail -f /var/www/html/Fitzone/backend/storage/logs/laravel.log
```

## 🔍 Debug:

Se não estiver funcionando, verifique:

1. **Arquivos existem?**
   ```bash
   ls -la /var/www/html/Fitzone/.htaccess
   ls -la /var/www/html/Fitzone/api/index.php
   ls -la /var/www/html/Fitzone/backend/public/index.php
   ```

2. **Permissões corretas?**
   ```bash
   chmod 644 /var/www/html/Fitzone/.htaccess
   chmod 755 /var/www/html/Fitzone/api/
   chmod 644 /var/www/html/Fitzone/api/index.php
   ```

3. **mod_rewrite habilitado?**
   - Verifique se o Apache tem mod_rewrite habilitado
   - No InfinityFree geralmente está habilitado

4. **Testar diretamente o Laravel:**
   ```bash
   curl https://fitzone.wuaze.com/backend/public/index.php
   ```
   (Isso pode não funcionar se o .htaccess do Laravel bloquear acesso direto)

## 📝 Fluxo Esperado:

```
1. Requisição: GET https://fitzone.wuaze.com/api/v1/exercises
2. .htaccess captura: /api/v1/exercises
3. Redireciona para: api/index.php
4. api/index.php processa:
   - Remove /api → /v1/exercises
   - Ajusta $_SERVER['REQUEST_URI'] = '/v1/exercises'
   - Inclui: backend/public/index.php
5. Laravel processa: /v1/exercises
6. Retorna JSON
```

## 🐛 Problemas Comuns:

### Erro: 404 HTML
- **Causa:** .htaccess não está funcionando
- **Solução:** Verificar se mod_rewrite está habilitado

### Erro: "Backend Laravel não encontrado"
- **Causa:** Caminho incorreto
- **Solução:** Verificar se `backend/public/index.php` existe

### Erro: "Route not found"
- **Causa:** Laravel não está recebendo a URI correta
- **Solução:** Verificar se `api/index.php` está ajustando `REQUEST_URI` corretamente

### Erro: CORS ainda bloqueando
- **Causa:** CORS não configurado corretamente
- **Solução:** Verificar `backend/config/cors.php` e limpar cache


