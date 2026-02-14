// Configuração da API
// Usa window.APP_CONFIG se disponível (carregado de config.js)
if (typeof window.APP_CONFIG !== 'undefined' && window.APP_CONFIG.API_URL) {
    window.API_URL = window.APP_CONFIG.API_URL;
} else if (typeof window.API_URL === 'undefined') {
    // Fallback: usar /api/index.php/v1 porque mod_rewrite pode não estar funcionando no InfinityFree
    window.API_URL = 'https://fitzone.wuaze.com/api/index.php/v1';
}

// Verificar se já está logado
document.addEventListener('DOMContentLoaded', () => {
    const pathname = window.location.pathname;
    const currentPage = pathname.split('/').pop() || '';
    const isRoot = pathname === '/' || pathname.endsWith('/') || currentPage === '' || currentPage === 'index.html';
    
    // Verificar se está na landing page ANTES de qualquer outra lógica
    const isLandingPage = document.querySelector('.landing-page') !== null || 
                          document.getElementById('login') !== null ||
                          pathname.includes('landing.html') ||
                          currentPage === 'landing.html';
    
    // Se está na landing page, NÃO fazer nenhuma verificação de autenticação
    // A landing page é sempre pública e não deve ser redirecionada
    if (isLandingPage) {
        // Apenas setup do formulário de login se existir (mas não na landing, ela tem seu próprio)
        // A landing page usa landing.js para gerenciar o login
        return;
    }
    
    const token = localStorage.getItem('token');
    
    // Se está na raiz ou index.html, redirecionar para landing (se não for landing já)
    if (isRoot && !isLandingPage) {
        // index.html já tem redirecionamento, mas garantir aqui também
        if (currentPage === 'index.html' || isRoot) {
            window.location.replace('landing.html');
            return;
        }
    }
    
    // Se está em página de login/register e já tem token, redirecionar para dashboard
    if ((currentPage === 'login.html' || currentPage === 'register.html') && token) {
        redirectToDashboard();
        return;
    }
    
    // Se está em página protegida e não tem token, redirecionar para landing
    const publicPages = ['login.html', 'register.html', 'landing.html', 'index.html', ''];
    const isPublicPage = publicPages.includes(currentPage) || isRoot;
    
    if (!isPublicPage && !token) {
        window.location.href = 'landing.html';
        return;
    }

    // Setup formulários (apenas para login.html e register.html)
    const loginForm = document.getElementById('loginForm');
    if (loginForm && (currentPage === 'login.html')) {
        loginForm.addEventListener('submit', handleLogin);
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm && (currentPage === 'register.html')) {
        registerForm.addEventListener('submit', handleRegister);
    }
});

// Login
async function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    showLoading();

    try {
        const url = `${window.API_URL}/login`;
        console.log('🔐 [handleLogin] Fazendo login:', url);
        console.log('🔐 [handleLogin] Email:', email);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        console.log('🔐 [handleLogin] Response status:', response.status);
        console.log('🔐 [handleLogin] Response headers:', Object.fromEntries(response.headers.entries()));
        
        if (!response.ok) {
            const text = await response.text();
            console.error('❌ [handleLogin] Response não OK:', response.status, text);
            throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
        }

        const data = await response.json();
        console.log('🔐 [handleLogin] Resposta recebida:', data);

        if (data.success) {
            // Salvar token e user no localStorage
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            showSuccess('Login realizado com sucesso!');
            
            // Redirecionar para dashboard
            setTimeout(() => {
                redirectToDashboard();
            }, 500);
        } else {
            showError(data.message || 'Erro ao fazer login');
        }
    } catch (error) {
        console.error('Erro:', error);
        showError('Erro ao conectar com o servidor');
    } finally {
        hideLoading();
    }
}

// Registro
async function handleRegister(e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const password_confirmation = document.getElementById('password_confirmation').value;
    const role = document.getElementById('role').value;
    const phone = document.getElementById('phone').value;

    if (password !== password_confirmation) {
        showError('As senhas não coincidem');
        return;
    }

    showLoading();

    try {
        const response = await fetch(`${window.API_URL}/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                name, 
                email, 
                password, 
                password_confirmation,
                role,
                phone 
            })
        });

        const data = await response.json();

        if (data.success) {
            // Salvar token e user no localStorage
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            showSuccess('Cadastro realizado com sucesso!');
            
            // Redirecionar para dashboard
            setTimeout(() => {
                redirectToDashboard();
            }, 500);
        } else {
            showError(data.message || 'Erro ao criar conta');
        }
    } catch (error) {
        console.error('Erro:', error);
        showError('Erro ao conectar com o servidor');
    } finally {
        hideLoading();
    }
}

// Logout
async function logout() {
    const token = localStorage.getItem('token');
    
    if (token) {
        try {
            await fetch(`${window.API_URL}/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                }
            });
        } catch (error) {
            console.error('Erro ao fazer logout:', error);
        }
    }
    
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'landing.html';
}

// Redirecionar para dashboard correto
function redirectToDashboard() {
    const userStr = localStorage.getItem('user');
    if (!userStr) {
        window.location.href = 'landing.html';
        return;
    }
    
    const user = JSON.parse(userStr);
    
    if (user.role === 'personal') {
        window.location.href = 'dashboard-personal.html';
    } else {
        window.location.href = 'dashboard-cliente.html';
    }
}

// Obter usuário autenticado
function getAuthUser() {
    const userStr = localStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
}

// Obter token
function getAuthToken() {
    return localStorage.getItem('token');
}

// Verificar se está autenticado
function isAuthenticated() {
    return !!getAuthToken();
}

// Headers com autenticação
function getAuthHeaders() {
    return {
        'Authorization': `Bearer ${getAuthToken()}`,
        'Content-Type': 'application/json',
    };
}

// Utility Functions
function showLoading() {
    document.getElementById('loading').classList.add('active');
}

function hideLoading() {
    document.getElementById('loading').classList.remove('active');
}

function showSuccess(message) {
    alert('✅ ' + message);
}

function showError(message) {
    alert('❌ ' + message);
}
