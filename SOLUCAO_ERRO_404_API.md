# 🔧 Solução para Erro 404 nas Rotas da API

## 📋 Problema Identificado

O frontend está recebendo erros 404 ao tentar acessar as rotas da API:
- `/api/v1/exercises`
- `/api/v1/workouts?user_id=1`
- `/api/v1/workout-plans?user_id=1`

O erro mostra: `Failed to load resource: the server responded with a status of 404 (Not Found)` e `SyntaxError: Unexpected token '<', "<!DOCTYPE "... is not valid JSON`.

## 🔍 Causa Raiz

Existem **dois problemas principais**:

### 1. Apache não está roteando para `api.php`

O Apache está retornando 404 antes mesmo de chegar ao arquivo `api.php`. Isso acontece porque:
- O módulo `mod_rewrite` do Apache não está habilitado
- O `.htaccess` não está sendo processado corretamente

**Solução**: Habilitar o `mod_rewrite` no Apache:
```bash
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### 2. Rotas protegidas por autenticação

As rotas da API estão protegidas com `auth:sanctum`, mas o frontend está tentando acessá-las sem autenticação. Quando o `api.php` é chamado diretamente, ele retorna 401 (Unauthorized) em JSON, que é o comportamento correto.

## ✅ Correções Aplicadas

### 1. Arquivo `api.php` corrigido
- Agora mantém o prefixo `/api` nas rotas passadas para o Laravel
- Garante que as rotas tenham o formato `/api/v1/*`

### 2. Middleware de autenticação configurado
- Configurado para retornar JSON (401) em vez de redirecionar para login
- Ajustado em `backend/bootstrap/app.php`

### 3. `.htaccess` atualizado
- Prioridade para rotas `/api/*` antes de outras regras
- Fallback caso `mod_rewrite` não esteja disponível

## 🚀 Soluções Possíveis

### Opção 1: Habilitar mod_rewrite (Recomendado)

Execute como root/sudo:
```bash
sudo a2enmod rewrite
sudo systemctl reload apache2
```

Depois teste:
```bash
curl -H "Accept: application/json" http://localhost/api/v1/exercises
```

### Opção 2: Tornar rotas públicas temporariamente

Se você quiser testar sem autenticação, mova as rotas para fora do middleware `auth:sanctum` em `backend/routes/api.php`:

```php
// Rotas públicas (sem autenticação)
Route::prefix('v1')->group(function () {
    Route::apiResource('exercises', ExerciseController::class);
    Route::get('workouts', [WorkoutController::class, 'index']);
    Route::get('workout-plans', [WorkoutPlanController::class, 'index']);
    // ... outras rotas
});
```

**⚠️ ATENÇÃO**: Isso remove a segurança. Use apenas para desenvolvimento!

### Opção 3: Implementar autenticação no frontend

O frontend precisa fazer login primeiro e enviar o token nas requisições:

1. Fazer login em `/api/v1/login`
2. Armazenar o token retornado
3. Incluir o token no header `Authorization: Bearer {token}` em todas as requisições

Exemplo no `app.js`:
```javascript
const token = localStorage.getItem('auth_token');
const response = await fetch(`${window.API_URL}/exercises`, {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    }
});
```

## 🧪 Testando

### Teste 1: Verificar se api.php funciona diretamente
```bash
cd /var/www/html/Fitzone
php -r "\$_SERVER['REQUEST_URI'] = '/api/v1/exercises'; \$_SERVER['HTTP_ACCEPT'] = 'application/json'; require 'api.php';"
```

**Resultado esperado**: JSON com `{"success":false,"message":"Não autenticado..."}` (401)

### Teste 2: Verificar rotas do Laravel
```bash
cd /var/www/html/Fitzone/backend
php artisan route:list --path=api/v1/exercises
```

**Resultado esperado**: Lista de rotas da API

### Teste 3: Testar via HTTP (após habilitar mod_rewrite)
```bash
curl -H "Accept: application/json" http://localhost/api/v1/exercises
```

**Resultado esperado**: JSON 401 ou dados se autenticado

## 📝 Status Atual

- ✅ `api.php` corrigido para manter prefixo `/api`
- ✅ Middleware configurado para retornar JSON
- ✅ `.htaccess` atualizado
- ⚠️ **PENDENTE**: Habilitar `mod_rewrite` no Apache (requer sudo)
- ⚠️ **PENDENTE**: Implementar autenticação no frontend OU tornar rotas públicas temporariamente

## 🎯 Próximos Passos

1. **Imediato**: Habilitar `mod_rewrite` no Apache
2. **Curto prazo**: Implementar autenticação no frontend OU tornar rotas públicas para desenvolvimento
3. **Longo prazo**: Implementar sistema completo de autenticação com tokens

