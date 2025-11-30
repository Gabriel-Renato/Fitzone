# 🔍 Guia de Debug - FitZone

## 📋 Logs Adicionados

Adicionei logs detalhados em vários pontos do sistema para facilitar o debug:

### 1. Frontend (JavaScript)
- ✅ `app.js` - Logs em todas as funções de API
- ✅ `auth.js` - Logs no processo de login
- ✅ Console do navegador mostra todos os passos

### 2. Backend (PHP)
- ✅ `api/index.php` - Logs de requisições recebidas
- ✅ Arquivo de log: `backend/storage/logs/api-debug.log`

## 🧪 Como Usar os Logs

### No Navegador (Console)
1. Abra o DevTools (F12)
2. Vá na aba "Console"
3. Você verá logs com emojis:
   - 🔵 = Exercícios
   - 🟢 = Treinos
   - 🟡 = Planos
   - 🔐 = Autenticação
   - ✅ = Sucesso
   - ❌ = Erro
   - ⚠️ = Aviso

### Verificar Logs do Backend
```bash
# Logs da API
tail -f /var/www/html/Fitzone/backend/storage/logs/api-debug.log

# Logs do Laravel
tail -f /var/www/html/Fitzone/backend/storage/logs/laravel.log
```

## 🔍 Verificar Banco de Dados

### Opção 1: Script de Teste (Recomendado)
Acesse no navegador:
```
https://fitzone.wuaze.com/backend/check-database.php
```

Este script mostra:
- ✅ Configuração do .env
- ✅ Extensões PHP carregadas
- ✅ Teste de conexão
- ✅ Tabelas existentes
- ✅ Logs recentes

### Opção 2: Via Laravel Artisan
```bash
cd /var/www/html/Fitzone/backend
php artisan db:show
php artisan migrate:status
```

## 📝 O que Verificar

### 1. Se as Requisições Estão Chegando
- Verifique `api-debug.log` para ver se as requisições chegam ao backend
- Verifique o console do navegador para ver as URLs sendo chamadas

### 2. Se o Banco Está Conectado
- Acesse `check-database.php`
- Verifique se mostra "✅ CONEXÃO ESTABELECIDA"
- Verifique se as tabelas existem

### 3. Se a Autenticação Está Funcionando
- Console do navegador mostra se o token está presente
- Verifique se o token é enviado nas requisições
- Verifique se recebe 401 (não autenticado) ou 200 (sucesso)

### 4. Se as Rotas Estão Funcionando
- Console mostra a URL completa sendo chamada
- Verifique se a URL está correta: `/api/index.php/v1/...`
- Verifique o status da resposta (200, 401, 404, 500)

## 🐛 Problemas Comuns

### Erro 404
- **Causa**: Rota não encontrada
- **Solução**: Verificar se está usando `/api/index.php/v1/...`

### Erro 401
- **Causa**: Não autenticado
- **Solução**: Fazer login primeiro

### Erro 500
- **Causa**: Erro no servidor/banco
- **Solução**: Verificar logs do Laravel e conexão com banco

### "could not find driver"
- **Causa**: Extensão PDO MySQL não carregada
- **Solução**: Verificar `check-database.php` para ver extensões

## 📊 Exemplo de Logs Esperados

### Console do Navegador (Sucesso)
```
🚀 [initializeApp] Iniciando aplicação...
🚀 [initializeApp] API_URL: https://fitzone.wuaze.com/api/index.php/v1
🚀 [initializeApp] Token: Presente
🔵 [loadExercises] Iniciando requisição: https://fitzone.wuaze.com/api/index.php/v1/exercises
🔵 [loadExercises] Response status: 200
✅ [loadExercises] Dados recebidos: {success: true, data: [...]}
✅ [loadExercises] Exercícios carregados: 5
```

### Log do Backend (api-debug.log)
```
2025-11-30 14:30:00 - API Request
REQUEST_URI: /api/index.php/v1/exercises
PATH_INFO: /v1/exercises
REQUEST_METHOD: GET
HTTP_ACCEPT: application/json
---
Processed API Path: /api/v1/exercises
---
```

## 🎯 Próximos Passos

1. Abra o console do navegador (F12)
2. Recarregue a página
3. Veja os logs no console
4. Acesse `check-database.php` para verificar o banco
5. Compartilhe os logs se precisar de ajuda!

