// Configuração da API
// Usando /api/index.php/v1 porque mod_rewrite pode não estar funcionando no InfinityFree
if (typeof window.API_URL === 'undefined') {
    window.API_URL = 'https://fitzone.wuaze.com/api/index.php/v1';
}

// Verificar se já está logado
document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    const currentPage = window.location.pathname.split('/').pop();
    
    // Se está em página de login e já tem token, redirecionar para dashboard
    if ((currentPage === 'login.html' || currentPage === 'register.html') && token) {
        redirectToDashboard();
    }
    
    // Se está em página protegida e não tem token, redirecionar para login
    if (currentPage !== 'login.html' && currentPage !== 'register.html' && currentPage !== '' && !token) {
        window.location.href = 'login.html';
    }

    // Setup formulários
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
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
    window.location.href = 'login.html';
}

// Redirecionar para dashboard correto
function redirectToDashboard() {
    const userStr = localStorage.getItem('user');
    if (!userStr) {
        window.location.href = 'login.html';
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
