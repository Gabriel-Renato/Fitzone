// Configuração da API
const API_URL = 'https://laravel-backend-production-a6ef.up.railway.app/api/v1';

// Aguardar carregamento das funções de autenticação
function waitForAuth() {
    return new Promise((resolve) => {
        const checkAuth = () => {
            if (typeof isAuthenticated === 'function' && typeof getAuthUser === 'function') {
                resolve();
            } else {
                setTimeout(checkAuth, 10);
            }
        };
        checkAuth();
    });
}

// Verificar autenticação após carregamento
async function checkAuthentication() {
    await waitForAuth();
    
    if (!isAuthenticated()) {
        window.location.href = 'login.html';
        return false;
    }

    const user = getAuthUser();
    if (user.role !== 'personal') {
        window.location.href = 'dashboard-cliente.html';
        return false;
    }
    
    return true;
}

// Estado
let clients = [];
let exercises = [];
let workouts = [];

// Inicialização
document.addEventListener('DOMContentLoaded', async () => {
    const isAuth = await checkAuthentication();
    if (!isAuth) return;
    
    const user = getAuthUser();
    document.getElementById('userName').textContent = user.name;
    initializeApp();
    setupNavigation();
    setupForms();
});

async function initializeApp() {
    showLoading();
    try {
        await loadClients();
        await loadExercises();
        await loadWorkouts();
    } catch (error) {
        showError('Erro ao carregar dados');
        console.error(error);
    } finally {
        hideLoading();
    }
}

// Navegação
function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = link.getAttribute('href').substring(1);
            showSection(target);
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        });
    });
}

function showSection(sectionId) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => section.classList.remove('active'));
    document.getElementById(sectionId).classList.add('active');
}

// Carregar Clientes
async function loadClients() {
    try {
        const response = await fetch(`${API_URL}/clients`, {
            headers: getAuthHeaders()
        });
        const data = await response.json();
        clients = data.data || [];
        renderClients();
    } catch (error) {
        console.error('Erro ao carregar clientes:', error);
    }
}

function renderClients() {
    const container = document.getElementById('clientsList');
    
    if (clients.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <div class="empty-state-title">Nenhum cliente cadastrado</div>
                <p>Adicione seu primeiro cliente para começar!</p>
            </div>
        `;
        return;
    }

    container.innerHTML = clients.map(client => `
        <div class="card">
            <div class="card-title">${client.name}</div>
            <p class="card-description">${client.email}</p>
            ${client.phone ? `<p class="card-description">📱 ${client.phone}</p>` : ''}
            <div class="card-meta">
                <span class="meta-item">${client.workout_logs_count || 0} treinos realizados</span>
            </div>
            <div class="card-actions">
                <button class="btn btn-primary btn-small" onclick="viewClient(${client.id})">Ver Detalhes</button>
            </div>
        </div>
    `).join('');
}

// Adicionar Cliente
function showAddClientModal() {
    document.getElementById('clientForm').reset();
    showModal('clientModal');
}


function setupForms() {
    // Form de criar cliente
    document.getElementById('clientForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const data = {
            name: document.getElementById('clientName').value,
            email: document.getElementById('clientEmail').value,
            password: document.getElementById('clientPassword').value,
            phone: document.getElementById('clientPhone').value,
            goals: document.getElementById('clientGoals').value,
        };

        showLoading();
        try {
            const response = await fetch(`${API_URL}/clients`, {
                method: 'POST',
                headers: getAuthHeaders(),
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                showSuccess('Cliente criado com sucesso!');
                await loadClients();
                closeModal('clientModal');
            } else {
                showError(result.message || 'Erro ao criar cliente');
            }
        } catch (error) {
            showError('Erro ao criar cliente');
            console.error(error);
        } finally {
            hideLoading();
        }
    });

    // Form de atribuir treino
    document.getElementById('assignWorkoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const clientId = parseInt(document.getElementById('assignClientId').value);
        const workoutId = parseInt(document.getElementById('assignWorkoutSelect').value);
        const dayOfWeek = document.getElementById('assignDayOfWeek').value;
        const scheduledTime = document.getElementById('assignScheduledTime').value;

        const data = {
            user_id: clientId,
            workout_id: workoutId,
            day_of_week: dayOfWeek,
            scheduled_time: scheduledTime || null,
            is_active: true
        };

        showLoading();
        try {
            const response = await fetch(`${API_URL}/workout-plans`, {
                method: 'POST',
                headers: getAuthHeaders(),
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                showSuccess('Treino atribuído com sucesso!');
                closeModal('assignWorkoutModal');
                // Recarregar detalhes do cliente
                const currentClientId = document.getElementById('assignClientId').value;
                await viewClient(currentClientId);
            } else {
                showError(result.message || 'Erro ao atribuir treino');
            }
        } catch (error) {
            showError('Erro ao atribuir treino');
            console.error(error);
        } finally {
            hideLoading();
        }
    });
}

// Carregar exercícios e treinos (reutilizar lógica do app.js original)
async function loadExercises() {
    try {
        const response = await fetch(`${API_URL}/exercises`, {
            headers: getAuthHeaders()
        });
        const data = await response.json();
        exercises = data.data || [];
        renderExercises();
    } catch (error) {
        console.error('Erro ao carregar exercícios:', error);
    }
}

function renderExercises() {
    const container = document.getElementById('exercisesList');
    container.innerHTML = exercises.map(exercise => `
        <div class="card">
            <div class="card-title">${exercise.name}</div>
            <span class="card-badge">${exercise.muscle_group}</span>
            ${exercise.description ? `<p class="card-description">${exercise.description}</p>` : ''}
        </div>
    `).join('');
}

async function loadWorkouts() {
    try {
        const response = await fetch(`${API_URL}/workouts?user_id=${user.id}`, {
            headers: getAuthHeaders()
        });
        const data = await response.json();
        workouts = data.data || [];
        renderWorkouts();
    } catch (error) {
        console.error('Erro ao carregar treinos:', error);
    }
}

function renderWorkouts() {
    const container = document.getElementById('workoutsList');
    container.innerHTML = workouts.map(workout => `
        <div class="workout-card">
            <div class="workout-title">${workout.name}</div>
            <span class="workout-focus">${workout.focus}</span>
            ${workout.description ? `<p class="card-description">${workout.description}</p>` : ''}
        </div>
    `).join('');
}

async function viewClient(id) {
    showLoading();
    try {
        const response = await fetch(`${API_URL}/clients/${id}`, {
            headers: getAuthHeaders()
        });
        
        const data = await response.json();
        
        if (data.success) {
            renderClientDetails(data.data);
            showModal('clientDetailsModal');
        } else {
            showError('Erro ao carregar dados do cliente');
        }
    } catch (error) {
        console.error('Erro ao carregar cliente:', error);
        showError('Erro ao carregar dados do cliente');
    } finally {
        hideLoading();
    }
}

function renderClientDetails(client) {
    const container = document.getElementById('clientDetailsContent');
    
    // Calcular estatísticas
    const totalWorkouts = client.workout_logs ? client.workout_logs.length : 0;
    const avgRating = client.workout_logs && client.workout_logs.length > 0
        ? (client.workout_logs.reduce((sum, log) => sum + (log.rating || 0), 0) / client.workout_logs.length).toFixed(1)
        : '0';
    
    // Plano semanal
    const weeklyPlan = client.workout_plans || [];
    const days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
    
    container.innerHTML = `
        <!-- Informações do Cliente -->
        <div class="client-info-card">
            <h3>${client.name}</h3>
            <div class="client-info-grid">
                <div class="info-item">
                    <span class="info-label">📧 E-mail:</span>
                    <span class="info-value">${client.email}</span>
                </div>
                ${client.phone ? `
                <div class="info-item">
                    <span class="info-label">📱 Telefone:</span>
                    <span class="info-value">${client.phone}</span>
                </div>
                ` : ''}
                ${client.pivot && client.pivot.goals ? `
                <div class="info-item full-width">
                    <span class="info-label">🎯 Objetivos:</span>
                    <span class="info-value">${client.pivot.goals}</span>
                </div>
                ` : ''}
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="stats-section">
            <h4>📊 Estatísticas</h4>
            <div class="stats-grid-small">
                <div class="stat-card-small">
                    <div class="stat-value">${totalWorkouts}</div>
                    <div class="stat-label">Treinos Realizados</div>
                </div>
                <div class="stat-card-small">
                    <div class="stat-value">${avgRating}⭐</div>
                    <div class="stat-label">Avaliação Média</div>
                </div>
            </div>
        </div>

        <!-- Plano Semanal -->
        <div class="plan-section">
            <h4>📅 Plano Semanal Atual</h4>
            ${weeklyPlan.length > 0 ? `
                <div class="weekly-plan-compact">
                    ${days.map(day => {
                        const plan = weeklyPlan.find(p => p.day_of_week === day);
                        if (plan && plan.workout) {
                            return `
                                <div class="day-compact">
                                    <strong>${day}</strong>
                                    <span>${plan.workout.name}</span>
                                    <span class="badge-small">${plan.workout.focus}</span>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="day-compact empty">
                                    <strong>${day}</strong>
                                    <span>Descanso</span>
                                </div>
                            `;
                        }
                    }).join('')}
                </div>
            ` : '<p class="text-muted">Nenhum treino programado ainda.</p>'}
        </div>

        <!-- Histórico Recente -->
        <div class="history-section">
            <h4>🏋️ Últimos Treinos (${client.workout_logs ? Math.min(5, client.workout_logs.length) : 0})</h4>
            ${client.workout_logs && client.workout_logs.length > 0 ? `
                <div class="history-list-compact">
                    ${client.workout_logs.slice(0, 5).map(log => `
                        <div class="history-item-compact">
                            <div class="history-item-header">
                                <strong>${log.workout ? log.workout.name : 'Treino'}</strong>
                                <span class="text-muted">${formatDate(log.completed_at)}</span>
                            </div>
                            <div class="history-item-details">
                                ${log.duration ? `⏱️ ${log.duration}min` : ''}
                                ${log.rating ? `⭐ ${log.rating}/5` : ''}
                            </div>
                            ${log.notes ? `<p class="history-notes-compact">${log.notes}</p>` : ''}
                        </div>
                    `).join('')}
                </div>
            ` : '<p class="text-muted">Nenhum treino realizado ainda.</p>'}
        </div>

        <!-- Ações -->
        <div class="client-actions">
            <button class="btn btn-primary" onclick="assignWorkout(${client.id})">
                📋 Atribuir Treino
            </button>
            <button class="btn btn-secondary" onclick="editClient(${client.id})">
                ✏️ Editar Cliente
            </button>
        </div>
    `;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

async function assignWorkout(clientId) {
    // Fechar modal de detalhes
    closeModal('clientDetailsModal');
    
    // Salvar ID do cliente
    document.getElementById('assignClientId').value = clientId;
    
    // Limpar formulário
    document.getElementById('assignWorkoutForm').reset();
    document.getElementById('assignClientId').value = clientId; // Manter o ID após reset
    
    // Popular select de treinos
    await populateWorkoutSelect();
    
    // Abrir modal
    showModal('assignWorkoutModal');
}

async function populateWorkoutSelect() {
    const select = document.getElementById('assignWorkoutSelect');
    select.innerHTML = '<option value="">Escolha um treino...</option>';
    
    if (workouts.length === 0) {
        select.innerHTML += '<option value="" disabled>Nenhum treino disponível. Crie um treino primeiro.</option>';
        return;
    }
    
    workouts.forEach(workout => {
        const option = document.createElement('option');
        option.value = workout.id;
        option.textContent = `${workout.name} - ${workout.focus}`;
        select.appendChild(option);
    });
}

function editClient(clientId) {
    closeModal('clientDetailsModal');
    alert('Editar cliente - Funcionalidade a ser implementada. Cliente ID: ' + clientId);
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

function showAddExerciseModal() {
    alert('Adicionar exercício - Use a tela original de exercícios');
}

function showCreateWorkoutModal() {
    alert('Criar treino - Use a tela original de treinos');
}

// Expor todas as funções necessárias globalmente
window.showAddClientModal = showAddClientModal;
window.showAddExerciseModal = showAddExerciseModal;
window.showCreateWorkoutModal = showCreateWorkoutModal;
window.showModal = showModal;
window.closeModal = closeModal;
