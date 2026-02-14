// Configuração da API
if (typeof window.API_URL === 'undefined') {
    window.API_URL = 'https://fitzone.wuaze.com/api/v1';
}

// Verificar autenticação
if (!isAuthenticated()) {
    window.location.href = 'landing.html';
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
let currentWorkoutData = null;
let completedExercises = [];

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
        const response = await fetch(`${window.API_URL}/workout-logs-stats`, {
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
        const response = await fetch(`${window.API_URL}/workout-plans?user_id=${user.id}`, {
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
            // Verificar se há check-in salvo
            const checkInKey = `checkin_workout_${plan.workout_id}`;
            const savedCheckIn = localStorage.getItem(checkInKey);
            let checkInBadge = '';
            
            if (savedCheckIn) {
                try {
                    const checkInData = JSON.parse(savedCheckIn);
                    const total = plan.workout.exercises ? plan.workout.exercises.length : 0;
                    const completed = checkInData.completedExercises ? checkInData.completedExercises.length : 0;
                    if (completed > 0) {
                        checkInBadge = `
                            <div class="checkin-progress-badge">
                                💾 ${completed}/${total} exercícios
                            </div>
                        `;
                    }
                } catch (error) {}
            }
            
            return `
                <div class="day-card clickable" onclick="viewWorkoutExercises(${plan.workout_id}, ${plan.id}, '${day}')">
                    <div class="day-name">${day}-feira</div>
                    ${plan.scheduled_time ? `<div class="day-time">⏰ ${plan.scheduled_time}</div>` : ''}
                    <div class="workout-title" style="font-size: 1rem; margin: 1rem 0;">${plan.workout.name}</div>
                    <span class="card-badge">${plan.workout.focus}</span>
                    <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--gray);">
                        📋 ${plan.workout.exercises ? plan.workout.exercises.length + ' exercícios' : 'Ver exercícios'}
                    </div>
                    ${checkInBadge}
                    <div class="click-hint">Clique para ver exercícios 👆</div>
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
        const response = await fetch(`${window.API_URL}/workout-logs`, {
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

// Visualizar exercícios do treino
async function viewWorkoutExercises(workoutId, workoutPlanId, dayName) {
    showLoading();
    try {
        const response = await fetch(`${window.API_URL}/workouts/${workoutId}`, {
            headers: getAuthHeaders()
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentWorkoutData = {
                workout: data.data,
                workoutPlanId: workoutPlanId,
                dayName: dayName
            };
            
            // Carregar progresso salvo (se houver)
            loadSavedCheckIn(workoutId);
            
            renderWorkoutExercises();
            showModal('workoutExercisesModal');
        } else {
            showError('Erro ao carregar exercícios');
        }
    } catch (error) {
        console.error('Erro ao carregar exercícios:', error);
        showError('Erro ao carregar exercícios');
    } finally {
        hideLoading();
    }
}

// Salvar check-in (progresso parcial)
function saveCheckIn() {
    if (!currentWorkoutData) return;
    
    const checkInData = {
        workoutId: currentWorkoutData.workout.id,
        completedExercises: completedExercises,
        savedAt: new Date().toISOString(),
        dayName: currentWorkoutData.dayName
    };
    
    // Salvar no localStorage
    const checkInKey = `checkin_workout_${currentWorkoutData.workout.id}`;
    localStorage.setItem(checkInKey, JSON.stringify(checkInData));
    
    // Feedback visual
    showSuccess(`Check-in salvo! ${completedExercises.length} exercícios marcados.`);
    
    // Adicionar badge de check-in salvo
    addCheckInBadge();
}

// Carregar check-in salvo
function loadSavedCheckIn(workoutId) {
    const checkInKey = `checkin_workout_${workoutId}`;
    const savedData = localStorage.getItem(checkInKey);
    
    if (savedData) {
        try {
            const checkInData = JSON.parse(savedData);
            completedExercises = checkInData.completedExercises || [];
            
            // Mostrar notificação de progresso recuperado
            const savedDate = new Date(checkInData.savedAt);
            const timeAgo = getTimeAgo(savedDate);
            console.log(`✅ Progresso recuperado: ${completedExercises.length} exercícios (${timeAgo})`);
        } catch (error) {
            console.error('Erro ao carregar check-in:', error);
            completedExercises = [];
        }
    } else {
        completedExercises = [];
    }
}

// Limpar check-in após concluir treino
function clearCheckIn(workoutId) {
    const checkInKey = `checkin_workout_${workoutId}`;
    localStorage.removeItem(checkInKey);
}

// Adicionar badge visual de check-in salvo
function addCheckInBadge() {
    const banner = document.querySelector('.workout-info-banner');
    if (banner) {
        const existingBadge = banner.querySelector('.checkin-badge');
        if (!existingBadge) {
            const badge = document.createElement('div');
            badge.className = 'checkin-badge';
            badge.innerHTML = '💾 Check-in salvo';
            banner.appendChild(badge);
            
            // Remover após 3 segundos
            setTimeout(() => {
                badge.style.opacity = '0';
                setTimeout(() => badge.remove(), 300);
            }, 3000);
        }
    }
}

// Calcular tempo decorrido
function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    
    if (seconds < 60) return 'agora mesmo';
    if (seconds < 3600) return `${Math.floor(seconds / 60)} minutos atrás`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)} horas atrás`;
    return `${Math.floor(seconds / 86400)} dias atrás`;
}

function renderWorkoutExercises() {
    if (!currentWorkoutData) return;
    
    const workout = currentWorkoutData.workout;
    
    // Atualizar título
    document.getElementById('workoutExercisesTitle').textContent = `🏋️ ${workout.name}`;
    
    // Verificar se há check-in salvo
    const checkInKey = `checkin_workout_${workout.id}`;
    const hasSavedCheckIn = localStorage.getItem(checkInKey);
    let savedInfo = '';
    
    if (hasSavedCheckIn && completedExercises.length > 0) {
        try {
            const checkInData = JSON.parse(hasSavedCheckIn);
            const savedDate = new Date(checkInData.savedAt);
            const timeAgo = getTimeAgo(savedDate);
            savedInfo = `
                <div class="saved-checkin-badge">
                    💾 Progresso recuperado: ${completedExercises.length} exercícios (${timeAgo})
                </div>
            `;
        } catch (error) {}
    }
    
    // Banner de informações
    document.getElementById('workoutInfoContent').innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <strong>${currentWorkoutData.dayName}-feira</strong>
                <span style="margin-left: 1rem;">•</span>
                <span style="margin-left: 1rem;">${workout.focus}</span>
            </div>
            <div style="font-size: 0.875rem; color: var(--gray);">
                ${workout.exercises ? workout.exercises.length : 0} exercícios
            </div>
        </div>
        ${savedInfo}
    `;
    
    // Lista de exercícios com checklist
    const container = document.getElementById('exercisesChecklistContent');
    
    if (!workout.exercises || workout.exercises.length === 0) {
        container.innerHTML = '<p class="text-muted">Nenhum exercício neste treino.</p>';
        return;
    }
    
    container.innerHTML = workout.exercises.map((exercise, index) => {
        const isChecked = completedExercises.includes(exercise.id);
        return `
            <div class="exercise-checklist-item ${isChecked ? 'checked' : ''}" onclick="toggleExercise(${exercise.id})">
                <div class="checkbox-container">
                    <input type="checkbox" ${isChecked ? 'checked' : ''} onclick="event.stopPropagation()">
                </div>
                <div class="exercise-details-full">
                    <div class="exercise-header-row">
                        <span class="exercise-order">${exercise.pivot.order}</span>
                        <h4 class="exercise-name">${exercise.name}</h4>
                        <span class="exercise-muscle">${exercise.muscle_group}</span>
                    </div>
                    <div class="exercise-specs">
                        <span class="spec-item">
                            <strong>Séries:</strong> ${exercise.pivot.sets}
                        </span>
                        <span class="spec-item">
                            <strong>Reps:</strong> ${exercise.pivot.reps}
                        </span>
                        ${exercise.pivot.weight ? `
                            <span class="spec-item">
                                <strong>Carga:</strong> ${exercise.pivot.weight} kg
                            </span>
                        ` : ''}
                        ${exercise.pivot.rest_time ? `
                            <span class="spec-item">
                                <strong>Descanso:</strong> ${exercise.pivot.rest_time}s
                            </span>
                        ` : ''}
                    </div>
                    ${exercise.pivot.notes ? `
                        <div class="exercise-notes">
                            💡 ${exercise.pivot.notes}
                        </div>
                    ` : ''}
                    ${exercise.description ? `
                        <div class="exercise-description">
                            ${exercise.description}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');
    
    // Adicionar contador de progresso
    updateProgressCounter();
}

function toggleExercise(exerciseId) {
    const index = completedExercises.indexOf(exerciseId);
    if (index > -1) {
        completedExercises.splice(index, 1);
    } else {
        completedExercises.push(exerciseId);
    }
    renderWorkoutExercises();
}

function updateProgressCounter() {
    if (!currentWorkoutData || !currentWorkoutData.workout.exercises) return;
    
    const total = currentWorkoutData.workout.exercises.length;
    const completed = completedExercises.length;
    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
    
    const counterHtml = `
        <div class="progress-counter">
            <div class="progress-text">
                Progresso: ${completed} de ${total} exercícios (${percentage}%)
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: ${percentage}%"></div>
            </div>
        </div>
    `;
    
    // Inserir antes da lista de exercícios se não existir
    const container = document.getElementById('exercisesChecklistContent');
    const existingCounter = container.querySelector('.progress-counter');
    if (existingCounter) {
        existingCounter.outerHTML = counterHtml;
    } else {
        container.insertAdjacentHTML('afterbegin', counterHtml);
    }
}

function openCompleteWorkoutFromExercises() {
    if (!currentWorkoutData) return;
    
    // Fechar modal de exercícios
    closeModal('workoutExercisesModal');
    
    // Abrir modal de conclusão
    const workout = currentWorkoutData.workout;
    document.getElementById('workoutId').value = workout.id;
    document.getElementById('workoutPlanId').value = currentWorkoutData.workoutPlanId;
    
    // Mostrar resumo dos exercícios completados
    renderCompletedExercisesSummary();
    
    // Reset outros campos
    selectedRating = 0;
    updateRatingDisplay();
    document.getElementById('duration').value = '';
    document.getElementById('notes').value = '';
    
    showModal('completeWorkoutModal');
}

function renderCompletedExercisesSummary() {
    const container = document.getElementById('completedExercisesSummary');
    
    if (!currentWorkoutData || !currentWorkoutData.workout.exercises) {
        container.innerHTML = '<p class="text-muted">Nenhum exercício selecionado</p>';
        return;
    }
    
    const completedExs = currentWorkoutData.workout.exercises.filter(ex => 
        completedExercises.includes(ex.id)
    );
    
    const total = currentWorkoutData.workout.exercises.length;
    const completed = completedExs.length;
    
    container.innerHTML = `
        <div class="summary-stats">
            <strong>${completed} de ${total} exercícios realizados</strong>
            ${completed < total ? `<span class="text-warning">(${total - completed} não completados)</span>` : ''}
        </div>
        ${completedExs.length > 0 ? `
            <ul class="completed-list">
                ${completedExs.map(ex => `
                    <li>✅ ${ex.name} - ${ex.pivot.sets}x${ex.pivot.reps}</li>
                `).join('')}
            </ul>
        ` : '<p class="text-muted">Você não marcou nenhum exercício como completo</p>'}
    `;
}

// Marcar treino como concluído (função original mantida)
function markAsComplete(workoutId, workoutPlanId) {
    document.getElementById('workoutId').value = workoutId;
    document.getElementById('workoutPlanId').value = workoutPlanId;
    document.getElementById('completeWorkoutForm').reset();
    completedExercises = [];
    selectedRating = 0;
    updateRatingDisplay();
    document.getElementById('completedExercisesSummary').innerHTML = '';
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
            exercises_completed: completedExercises.length > 0 ? completedExercises : null,
        };

        showLoading();
        try {
            const response = await fetch(`${window.API_URL}/workout-logs`, {
                method: 'POST',
                headers: getAuthHeaders(),
                body: JSON.stringify(data)
            });

            const result = await response.json();
            if (result.success) {
                // Limpar check-in salvo
                if (currentWorkoutData && currentWorkoutData.workout) {
                    clearCheckIn(currentWorkoutData.workout.id);
                }
                
                showSuccess('Treino registrado com sucesso! 🎉');
                await loadStats();
                await loadWorkoutLogs();
                closeModal('completeWorkoutModal');
                
                // Resetar estado
                currentWorkoutData = null;
                completedExercises = [];
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
