// Verificar autenticação
if (!isAuthenticated()) {
    window.location.href = 'login.html';
}

const user = getAuthUser();
if (user.role !== 'cliente') {
    window.location.href = 'dashboard-personal.html';
}

// Estado
let workoutPlans = [];
let workoutLogs = [];
let stats = {};
let selectedRating = 0;

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('userName').textContent = user.name;
    initializeApp();
    setupNavigation();
    setupForms();
    setupRating();
});

async function initializeApp() {
    showLoading();
    try {
        await loadStats();
        await loadWorkoutPlans();
        await loadWorkoutLogs();
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

// Carregar estatísticas
async function loadStats() {
    try {
        const response = await fetch(`${API_URL}/workout-logs-stats`, {
            headers: getAuthHeaders()
        });
        const data = await response.json();
        stats = data.data || {};
        renderStats();
    } catch (error) {
        console.error('Erro ao carregar estatísticas:', error);
    }
}

function renderStats() {
    document.getElementById('totalWorkouts').textContent = stats.total_workouts || 0;
    document.getElementById('thisWeek').textContent = stats.this_week || 0;
    document.getElementById('avgRating').textContent = stats.avg_rating ? stats.avg_rating.toFixed(1) : '0';
}

// Carregar plano semanal
async function loadWorkoutPlans() {
    try {
        const response = await fetch(`${API_URL}/workout-plans?user_id=${user.id}`, {
            headers: getAuthHeaders()
        });
        const data = await response.json();
        workoutPlans = data.data || [];
        renderWeeklyPlan();
    } catch (error) {
        console.error('Erro ao carregar plano:', error);
    }
}

function renderWeeklyPlan() {
    const container = document.getElementById('weeklyPlan');
    const days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];

    container.innerHTML = days.map(day => {
        const plan = workoutPlans.find(p => p.day_of_week === day);
        
        if (plan && plan.workout) {
            return `
                <div class="day-card">
                    <div class="day-name">${day}-feira</div>
                    ${plan.scheduled_time ? `<div class="day-time">⏰ ${plan.scheduled_time}</div>` : ''}
                    <div class="workout-title" style="font-size: 1rem; margin: 1rem 0;">${plan.workout.name}</div>
                    <span class="card-badge">${plan.workout.focus}</span>
                    <div style="margin-top: 1rem;">
                        ${plan.workout.exercises ? plan.workout.exercises.length + ' exercícios' : ''}
                    </div>
                    <div class="card-actions" style="margin-top: 1rem;">
                        <button class="btn btn-success btn-small" onclick="markAsComplete(${plan.workout_id}, ${plan.id})">
                            ✓ Marcar como Feito
                        </button>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="day-card empty">
                    <div class="day-name">${day}-feira</div>
                    <p class="card-description">Descanso</p>
                </div>
            `;
        }
    }).join('');
}

// Carregar histórico
async function loadWorkoutLogs() {
    try {
        const response = await fetch(`${API_URL}/workout-logs`, {
            headers: getAuthHeaders()
        });
        const data = await response.json();
        workoutLogs = data.data || [];
        renderHistory();
    } catch (error) {
        console.error('Erro ao carregar histórico:', error);
    }
}

function renderHistory() {
    const container = document.getElementById('historyList');
    
    if (workoutLogs.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <div class="empty-state-title">Nenhum treino registrado</div>
                <p>Marque seus treinos como concluídos para ver seu histórico!</p>
            </div>
        `;
        return;
    }

    container.innerHTML = workoutLogs.map(log => `
        <div class="history-card">
            <div class="history-header">
                <div>
                    <div class="history-title">${log.workout.name}</div>
                    <div class="history-date">📅 ${formatDate(log.completed_at)}</div>
                </div>
                <div class="history-rating">
                    ${renderRatingStars(log.rating)}
                </div>
            </div>
            ${log.duration ? `<p><strong>⏱️ Duração:</strong> ${log.duration} minutos</p>` : ''}
            ${log.notes ? `<p class="history-notes">${log.notes}</p>` : ''}
        </div>
    `).join('');
}

function renderRatingStars(rating) {
    if (!rating) return '';
    return '⭐'.repeat(rating);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

// Marcar treino como concluído
function markAsComplete(workoutId, workoutPlanId) {
    document.getElementById('workoutId').value = workoutId;
    document.getElementById('workoutPlanId').value = workoutPlanId;
    document.getElementById('completeWorkoutForm').reset();
    selectedRating = 0;
    updateRatingDisplay();
    showModal('completeWorkoutModal');
}

// Setup do sistema de avaliação
function setupRating() {
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = parseInt(star.getAttribute('data-rating'));
            document.getElementById('rating').value = selectedRating;
            updateRatingDisplay();
        });
    });
}

function updateRatingDisplay() {
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < selectedRating) {
            star.style.color = '#f59e0b';
        } else {
            star.style.color = '#e5e7eb';
        }
    });
}

// Setup de formulários
function setupForms() {
    document.getElementById('completeWorkoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const data = {
            workout_id: parseInt(document.getElementById('workoutId').value),
            workout_plan_id: parseInt(document.getElementById('workoutPlanId').value) || null,
            completed_at: new Date().toISOString().split('T')[0],
            duration: parseInt(document.getElementById('duration').value) || null,
            rating: selectedRating || null,
            notes: document.getElementById('notes').value || null,
        };

        showLoading();
        try {
            const response = await fetch(`${API_URL}/workout-logs`, {
                method: 'POST',
                headers: getAuthHeaders(),
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                showSuccess('Treino registrado com sucesso! 🎉');
                await loadStats();
                await loadWorkoutLogs();
                closeModal('completeWorkoutModal');
            } else {
                showError(result.message || 'Erro ao registrar treino');
            }
        } catch (error) {
            showError('Erro ao registrar treino');
            console.error(error);
        } finally {
            hideLoading();
        }
    });
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}
