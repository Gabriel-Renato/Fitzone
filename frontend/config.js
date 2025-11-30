/**
 * Configuração do Frontend - FitZone
 * Este arquivo contém as variáveis de ambiente do frontend
 */

// Configuração da API
window.APP_CONFIG = {
    // URL da API - usar /api/index.php/v1 para InfinityFree
    API_URL: 'https://fitzone.wuaze.com/api/index.php/v1',
    
    // URL alternativa caso mod_rewrite funcione
    API_URL_ALTERNATIVE: 'https://fitzone.wuaze.com/api/v1',
    
    // Ambiente
    ENV: 'production',
    
    // Debug
    DEBUG: false,
    
    // Timeout das requisições (em ms)
    REQUEST_TIMEOUT: 30000,
    
    // Versão da API
    API_VERSION: 'v1'
};

// Compatibilidade com código existente
if (typeof window.API_URL === 'undefined') {
    window.API_URL = window.APP_CONFIG.API_URL;
}

// Log de configuração (apenas em desenvolvimento)
if (window.APP_CONFIG.DEBUG) {
    console.log('🔧 Configuração carregada:', window.APP_CONFIG);
}

