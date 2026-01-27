/**
 * Landing Page JavaScript - FitZone
 * Gerencia navegação, login e interações da landing page
 */

// Configuração da API (caso config.js não esteja carregado)
if (typeof window.API_URL === 'undefined') {
    if (typeof window.APP_CONFIG !== 'undefined' && window.APP_CONFIG.API_URL) {
        window.API_URL = window.APP_CONFIG.API_URL;
    } else {
        // Fallback
        window.API_URL = 'https://fitzone.wuaze.com/api/index.php/v1';
    }
}

// Setup da landing page (sempre acessível, sem necessidade de login)
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se usuário está logado e mostrar opções apropriadas
    checkUserStatus();
    
    // Setup do formulário de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLandingLogin);
    }
    
    // Setup de scroll suave para links de navegação
    setupSmoothScroll();
    
    // Setup de animações ao scroll
    setupScrollAnimations();
    
    // Setup do menu mobile
    setupMobileMenu();
});

/**
 * Verificar status do usuário e ajustar UI
 */
function checkUserStatus() {
    const token = localStorage.getItem('token');
    const userStr = localStorage.getItem('user');
    const loginSection = document.getElementById('login');
    
    if (token && userStr && loginSection) {
        try {
            const user = JSON.parse(userStr);
            const loginCard = loginSection.querySelector('.login-card');
            const loginForm = document.getElementById('loginForm');
            
            if (loginCard && loginForm) {
                // Criar seção para usuário logado
                const loggedInContent = document.createElement('div');
                loggedInContent.className = 'logged-in-content';
                loggedInContent.innerHTML = `
                    <div class="login-header">
                        <h2>💪 FitZone</h2>
                        <p>Bem-vindo de volta, ${user.name || 'Usuário'}!</p>
                    </div>
                    <div class="user-info">
                        <p class="user-role">${user.role === 'personal' ? '👨‍💼 Personal Trainer' : '👤 Cliente'}</p>
                    </div>
                    <div class="login-actions">
                        <button class="btn btn-primary btn-block btn-large" onclick="redirectToDashboard()">
                            Acessar Dashboard
                        </button>
                        <button class="btn btn-secondary btn-block" onclick="handleLogout()">
                            Sair
                        </button>
                    </div>
                `;
                
                // Esconder formulário e mostrar conteúdo para logado
                loginForm.style.display = 'none';
                loginCard.appendChild(loggedInContent);
            }
        } catch (error) {
            console.error('Erro ao verificar status do usuário:', error);
        }
    }
}

/**
 * Logout completo (independente do auth.js)
 */
async function logout() {
    const token = localStorage.getItem('token');
    
    if (token) {
        try {
            // Tentar fazer logout na API
            await fetch(`${window.API_URL}/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                }
            });
        } catch (error) {
            console.error('Erro ao fazer logout na API:', error);
            // Continuar mesmo se der erro na API
        }
    }
    
    // Limpar dados locais
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    
    // Recarregar a landing page
    window.location.href = 'landing.html';
}

/**
 * Handle logout na landing page
 * Função global para ser chamada de qualquer lugar
 */
function handleLogout() {
    if (confirm('Deseja realmente sair?')) {
        logout();
    }
}

// Tornar funções globais
window.handleLogout = handleLogout;
window.logout = logout;

/**
 * Handle login na landing page
 */
async function handleLandingLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('loginError');
    
    // Limpar erro anterior
    if (errorDiv) {
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
    }
    
    // Validação básica
    if (!email || !password) {
        showLoginError('Por favor, preencha todos os campos');
        return;
    }
    
    showLoading();
    
    try {
        const url = `${window.API_URL}/login`;
        console.log('🔐 [handleLandingLogin] Fazendo login:', url);
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        
        console.log('🔐 [handleLandingLogin] Response status:', response.status);
        
        if (!response.ok) {
            const text = await response.text();
            console.error('❌ [handleLandingLogin] Response não OK:', response.status, text);
            
            let errorMessage = 'Erro ao fazer login';
            try {
                const errorData = JSON.parse(text);
                errorMessage = errorData.message || errorMessage;
            } catch (e) {
                errorMessage = `Erro ${response.status}: ${text.substring(0, 100)}`;
            }
            
            throw new Error(errorMessage);
        }
        
        const data = await response.json();
        console.log('🔐 [handleLandingLogin] Resposta recebida:', data);
        
        if (data.success) {
            // Salvar token e user no localStorage
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            // Mostrar mensagem de sucesso
            showLoginSuccess('Login realizado com sucesso! Redirecionando...');
            
            // Redirecionar para dashboard após pequeno delay
            setTimeout(() => {
                redirectToDashboard();
            }, 1000);
        } else {
            showLoginError(data.message || 'Erro ao fazer login');
        }
    } catch (error) {
        console.error('Erro:', error);
        showLoginError(error.message || 'Erro ao conectar com o servidor. Verifique sua conexão.');
    } finally {
        hideLoading();
    }
}

/**
 * Mostrar erro no login
 */
function showLoginError(message) {
    const errorDiv = document.getElementById('loginError');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

/**
 * Mostrar sucesso no login
 */
function showLoginSuccess(message) {
    const errorDiv = document.getElementById('loginError');
    if (errorDiv) {
        errorDiv.style.background = '#d1fae5';
        errorDiv.style.color = '#065f46';
        errorDiv.style.borderColor = '#10b981';
        errorDiv.textContent = '✅ ' + message;
        errorDiv.style.display = 'block';
    }
}

/**
 * Redirecionar para dashboard correto
 * Função global para ser chamada de qualquer lugar
 */
function redirectToDashboard() {
    const userStr = localStorage.getItem('user');
    if (!userStr) {
        // Se não tem usuário, mostrar formulário de login
        const loginForm = document.getElementById('loginForm');
        const loggedInContent = document.querySelector('.logged-in-content');
        if (loginForm) loginForm.style.display = 'block';
        if (loggedInContent) loggedInContent.remove();
        return;
    }
    
    try {
        const user = JSON.parse(userStr);
        
        if (user.role === 'personal') {
            window.location.href = 'dashboard-personal.html';
        } else {
            window.location.href = 'dashboard-cliente.html';
        }
    } catch (error) {
        console.error('Erro ao redirecionar:', error);
        // Em caso de erro, redirecionar para landing sem recarregar
        window.location.href = 'landing.html';
    }
}

// Tornar função global
window.redirectToDashboard = redirectToDashboard;

/**
 * Scroll suave para seção de login
 */
function scrollToLogin() {
    const loginSection = document.getElementById('login');
    if (loginSection) {
        loginSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Focar no campo de email após scroll
        setTimeout(() => {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.focus();
            }
        }, 500);
    }
}

/**
 * Setup de scroll suave para links de navegação
 */
function setupSmoothScroll() {
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = link.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                const headerHeight = document.querySelector('.landing-header').offsetHeight;
                const targetPosition = targetSection.offsetTop - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Setup de animações ao scroll
 */
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observar cards de features e benefits
    const animatedElements = document.querySelectorAll('.feature-card, .benefit-item');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

/**
 * Setup do menu mobile
 */
function setupMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.querySelector('.landing-header .nav');
    
    if (mobileToggle && nav) {
        mobileToggle.addEventListener('click', () => {
            nav.classList.toggle('mobile-active');
            mobileToggle.classList.toggle('active');
        });
        
        // Fechar menu ao clicar em um link
        const navLinks = nav.querySelectorAll('.nav-link, .btn');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('mobile-active');
                mobileToggle.classList.remove('active');
            });
        });
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !mobileToggle.contains(e.target)) {
                nav.classList.remove('mobile-active');
                mobileToggle.classList.remove('active');
            }
        });
    }
}

/**
 * Toggle menu mobile
 */
function toggleMobileMenu() {
    const nav = document.querySelector('.landing-header .nav');
    const toggle = document.querySelector('.mobile-menu-toggle');
    
    if (nav && toggle) {
        nav.classList.toggle('mobile-active');
        toggle.classList.toggle('active');
    }
}

/**
 * Mostrar informações de registro
 */
function showRegisterInfo() {
    alert('Para criar uma conta, entre em contato com seu personal trainer ou administrador do sistema.');
}

/**
 * Utility: Mostrar loading
 */
function showLoading() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.classList.add('active');
    }
}

/**
 * Utility: Esconder loading
 */
function hideLoading() {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.classList.remove('active');
    }
}

// Adicionar estilos para menu mobile via JavaScript (caso necessário)
const mobileMenuStyles = `
    @media (max-width: 768px) {
        .landing-header .nav {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: var(--white);
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 999;
        }
        
        .landing-header .nav.mobile-active {
            transform: translateX(0);
        }
        
        .landing-header .nav ul {
            flex-direction: column;
            gap: 1rem;
        }
        
        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }
    }
`;

// Adicionar estilos ao head se não existirem
if (!document.getElementById('mobile-menu-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'mobile-menu-styles';
    styleSheet.textContent = mobileMenuStyles;
    document.head.appendChild(styleSheet);
}
