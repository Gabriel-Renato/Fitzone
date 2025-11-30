# 🌐 Solução para InfinityFree - Sem Acesso SSH/Sudo

## ⚠️ Limitação do InfinityFree

No **InfinityFree**, você **NÃO tem acesso SSH com sudo**, então não pode executar:
- `sudo a2enmod rewrite`
- `sudo systemctl reload apache2`

## ✅ Boa Notícia

O **InfinityFree geralmente já tem `mod_rewrite` habilitado por padrão**! O problema pode ser outra coisa.

## 🔧 Soluções Implementadas

Criei **duas soluções** que funcionam no InfinityFree:

### Solução 1: Usar `api.php` na raiz (Recomendado)

O arquivo `api.php` na raiz já está configurado. Se o `mod_rewrite` estiver funcionando, as rotas `/api/v1/*` devem funcionar automaticamente.

**Teste se está funcionando:**
```
https://seu-dominio.infinityfreeapp.com/api/v1/exercises
```

### Solução 2: Usar `/api/index.php` (Fallback)

Se o `mod_rewrite` não estiver funcionando, use esta estrutura:

**Acesse as rotas assim:**
```
https://seu-dominio.infinityfreeapp.com/api/index.php/v1/exercises
https://seu-dominio.infinityfreeapp.com/api/index.php/v1/workouts?user_id=1
https://seu-dominio.infinityfreeapp.com/api/index.php/v1/workout-plans?user_id=1
```

Criei o arquivo `/api/index.php` que funciona mesmo sem `mod_rewrite`.

## 🔄 Atualizar Frontend

Você precisa atualizar o `frontend/js/app.js` para usar a URL correta:

### Opção A: Se mod_rewrite funcionar (padrão)
```javascript
// Já está configurado assim:
window.API_URL = 'https://fitzone.wuaze.com/api/v1';
```

### Opção B: Se mod_rewrite NÃO funcionar
```javascript
// Mude para:
window.API_URL = 'https://fitzone.wuaze.com/api/index.php/v1';
```

## 🧪 Como Testar

### 1. Teste direto no navegador:
```
https://fitzone.wuaze.com/api/v1/exercises
```

**Se retornar JSON** (mesmo que seja erro 401): ✅ Funcionando!
**Se retornar 404 HTML**: ❌ Precisa usar a Solução 2

### 2. Teste com a Solução 2:
```
https://fitzone.wuaze.com/api/index.php/v1/exercises
```

**Se retornar JSON**: ✅ Funciona sem mod_rewrite!

## 📝 Checklist

- [ ] Testar `/api/v1/exercises` no navegador
- [ ] Se funcionar: manter configuração atual
- [ ] Se não funcionar: atualizar `app.js` para usar `/api/index.php/v1`
- [ ] Verificar se as rotas retornam 401 (não autenticado) em vez de 404
- [ ] Implementar autenticação no frontend OU tornar rotas públicas temporariamente

## 🚨 Importante sobre Autenticação

As rotas estão protegidas. Você tem 3 opções:

### Opção 1: Tornar rotas públicas (desenvolvimento)
Edite `backend/routes/api.php` e mova as rotas para fora do `middleware('auth:sanctum')`.

### Opção 2: Implementar autenticação no frontend
Faça login primeiro e inclua o token nas requisições.

### Opção 3: Testar com curl/Postman
Use ferramentas que permitem enviar headers de autenticação.

## 💡 Dica

No InfinityFree, o `mod_rewrite` geralmente **já está habilitado**. Se você está recebendo 404, pode ser:
1. Problema de configuração do `.htaccess`
2. O InfinityFree bloqueando certas regras
3. Problema de permissões de arquivo

A Solução 2 (`/api/index.php`) funciona **sempre**, independente de `mod_rewrite`!

