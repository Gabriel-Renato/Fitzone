# 🌐 Configuração do Frontend - FitZone

## 📋 Arquivo de Configuração

Criei o arquivo `frontend/config.js` que funciona como um `.env` para o frontend.

### Localização
```
/var/www/html/Fitzone/frontend/config.js
```

## 🔧 Configurações Disponíveis

O arquivo `config.js` contém:

```javascript
window.APP_CONFIG = {
    // URL da API
    API_URL: 'https://fitzone.wuaze.com/api/index.php/v1',
    
    // URL alternativa
    API_URL_ALTERNATIVE: 'https://fitzone.wuaze.com/api/v1',
    
    // Ambiente
    ENV: 'production',
    
    // Debug
    DEBUG: false,
    
    // Timeout das requisições
    REQUEST_TIMEOUT: 30000,
    
    // Versão da API
    API_VERSION: 'v1'
};
```

## 📝 Como Usar

### 1. Editar Configuração

Edite o arquivo `frontend/config.js` para alterar as configurações:

```javascript
// Para desenvolvimento local
API_URL: 'http://localhost/api/index.php/v1',

// Para produção
API_URL: 'https://fitzone.wuaze.com/api/index.php/v1',
```

### 2. Carregamento Automático

O `config.js` é carregado automaticamente em todas as páginas HTML:
- ✅ `index.html`
- ✅ `login.html`
- ✅ `dashboard-personal.html`
- ✅ `dashboard-cliente.html`

### 3. Uso no Código

Os arquivos JS (`app.js`, `auth.js`) já estão configurados para usar `window.APP_CONFIG`:

```javascript
// O código verifica automaticamente:
if (typeof window.APP_CONFIG !== 'undefined' && window.APP_CONFIG.API_URL) {
    window.API_URL = window.APP_CONFIG.API_URL;
}
```

## 🔄 Mudanças Aplicadas

1. ✅ Criado `frontend/config.js` com todas as configurações
2. ✅ Atualizado `app.js` para usar `APP_CONFIG`
3. ✅ Atualizado `auth.js` para usar `APP_CONFIG`
4. ✅ Adicionado `config.js` em todos os HTMLs

## 🎯 Benefícios

- ✅ **Centralizado**: Todas as configurações em um só lugar
- ✅ **Fácil de editar**: Basta editar `config.js`
- ✅ **Sem rebuild**: Mudanças são imediatas
- ✅ **Compatível**: Funciona com código existente

## 📝 Exemplo de Uso

Para mudar a URL da API, edite apenas o `config.js`:

```javascript
// Antes
API_URL: 'https://fitzone.wuaze.com/api/index.php/v1',

// Depois (se mod_rewrite funcionar)
API_URL: 'https://fitzone.wuaze.com/api/v1',
```

Todas as requisições usarão automaticamente a nova URL!

## ⚠️ Importante

- O arquivo `config.js` é público (não contém senhas)
- Para dados sensíveis, use variáveis do servidor
- Sempre teste após alterar configurações

