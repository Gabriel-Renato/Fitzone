// Configuração da API
// Usando /api/index.php/v1 porque mod_rewrite pode não estar funcionando no InfinityFree
if (typeof window.API_URL === 'undefined') {
    // Tenta primeiro a rota padrão, se falhar, usa a alternativa
    window.API_URL = 'https://fitzone.wuaze.com/api/index.php/v1';
    // Alternativa: 'https://fitzone.wuaze.com/api/v1' (se mod_rewrite funcionar)
}
const USER_ID = 1; // Temporário até implementar autenticação

// Estado da aplicação
let exercises = [];
let workouts = [];
let workoutPlans = [];
let selectedExercisesForWorkout = [];

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    initializeApp();
    setupNavigation();
    setupForms();
});

async function initializeApp() {
    showLoading();
    try {
        await loadExercises();
        await loadWorkouts();
        await loadWorkoutPlans();
        await loadMuscleGroups();
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

// API Calls - Exercises
async function loadExercises() {
    try {
        const response = await fetch(`${window.API_URL}/exercises`);
        const data = await response.json();
        exercises = data.data;
        renderExercises();
        populateExerciseSelect();
    } catch (error) {
        console.error('Erro ao carregar exercícios:', error);
    }
}

async function loadMuscleGroups() {
    const muscleGroups = [...new Set(exercises.map(e => e.muscle_group))];
    const select = document.getElementById('filterMuscleGroup');
    
    muscleGroups.forEach(group => {
        const option = document.createElement('option');
        option.value = group;
        option.textContent = group;
        select.appendChild(option);
    });

    select.addEventListener('change', filterExercises);
    document.getElementById('searchExercise').addEventListener('input', filterExercises);
}

function renderExercises(filteredExercises = null) {
    const container = document.getElementById('exercisesList');
    const exercisesToRender = filteredExercises || exercises;

    if (exercisesToRender.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">💪</div>
                <div class="empty-state-title">Nenhum exercício encontrado</div>
                <p>Adicione seu primeiro exercício para começar!</p>
            </div>
        `;
        return;
    }

    container.innerHTML = exercisesToRender.map(exercise => `
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">${exercise.name}</div>
                    <span class="card-badge">${exercise.muscle_group}</span>
                </div>
            </div>
            ${exercise.description ? `<p class="card-description">${exercise.description}</p>` : ''}
            ${exercise.equipment ? `
                <div class="card-meta">
                    <span class="meta-item">🏋️ ${exercise.equipment}</span>
                </div>
            ` : ''}
            <div class="card-actions">
                <button class="btn btn-danger btn-small" onclick="deleteExercise(${exercise.id})">Deletar</button>
            </div>
        </div>
    `).join('');
}

function filterExercises() {
    const search = document.getElementById('searchExercise').value.toLowerCase();
    const muscleGroup = document.getElementById('filterMuscleGroup').value;

    let filtered = exercises;

    if (search) {
        filtered = filtered.filter(e => 
            e.name.toLowerCase().includes(search) ||
            (e.description && e.description.toLowerCase().includes(search))
        );
    }

    if (muscleGroup) {
        filtered = filtered.filter(e => e.muscle_group === muscleGroup);
    }

    renderExercises(filtered);
}

async function createExercise(data) {
    try {
        const response = await fetch(`${window.API_URL}/exercises`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Exercício criado com sucesso!');
            await loadExercises();
            closeModal('exerciseModal');
        } else {
            showError('Erro ao criar exercício');
        }
    } catch (error) {
        showError('Erro ao criar exercício');
        console.error(error);
    }
}

async function deleteExercise(id) {
    if (!confirm('Tem certeza que deseja deletar este exercício?')) return;

    try {
        const response = await fetch(`${window.API_URL}/exercises/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Exercício deletado com sucesso!');
            await loadExercises();
        }
    } catch (error) {
        showError('Erro ao deletar exercício');
        console.error(error);
    }
}

// API Calls - Workouts
async function loadWorkouts() {
    try {
        const response = await fetch(`${window.API_URL}/workouts?user_id=${USER_ID}`);
        const data = await response.json();
        workouts = data.data;
        renderWorkouts();
        populateWorkoutSelect();
    } catch (error) {
        console.error('Erro ao carregar treinos:', error);
    }
}

function renderWorkouts() {
    const container = document.getElementById('workoutsList');

    if (workouts.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">🏋️</div>
                <div class="empty-state-title">Nenhum treino cadastrado</div>
                <p>Crie seu primeiro treino para começar!</p>
            </div>
        `;
        return;
    }

    container.innerHTML = workouts.map(workout => `
        <div class="workout-card">
            <div class="workout-header">
                <div>
                    <div class="workout-title">${workout.name}</div>
                    ${workout.description ? `<p class="card-description">${workout.description}</p>` : ''}
                </div>
                <div>
                    <span class="workout-focus">${workout.focus}</span>
                </div>
            </div>
            
            ${workout.exercises && workout.exercises.length > 0 ? `
                <table class="exercises-table">
                    <thead>
                        <tr>
                            <th>Exercício</th>
                            <th>Séries</th>
                            <th>Reps</th>
                            <th>Carga</th>
                            <th>Descanso</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${workout.exercises.map(ex => `
                            <tr>
                                <td><strong>${ex.name}</strong><br><small>${ex.muscle_group}</small></td>
                                <td>${ex.pivot.sets}</td>
                                <td>${ex.pivot.reps}</td>
                                <td>${ex.pivot.weight ? ex.pivot.weight + ' kg' : '-'}</td>
                                <td>${ex.pivot.rest_time ? ex.pivot.rest_time + 's' : '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            ` : '<p class="card-description">Nenhum exercício adicionado ainda.</p>'}
            
            <div class="card-actions">
                <button class="btn btn-danger btn-small" onclick="deleteWorkout(${workout.id})">Deletar Treino</button>
            </div>
        </div>
    `).join('');
}

async function createWorkout(data) {
    try {
        const response = await fetch(`${window.API_URL}/workouts`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Treino criado com sucesso!');
            await loadWorkouts();
            closeModal('workoutModal');
            selectedExercisesForWorkout = [];
        } else {
            showError('Erro ao criar treino');
        }
    } catch (error) {
        showError('Erro ao criar treino');
        console.error(error);
    }
}

async function deleteWorkout(id) {
    if (!confirm('Tem certeza que deseja deletar este treino?')) return;

    try {
        const response = await fetch(`${window.API_URL}/workouts/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Treino deletado com sucesso!');
            await loadWorkouts();
        }
    } catch (error) {
        showError('Erro ao deletar treino');
        console.error(error);
    }
}

// API Calls - Workout Plans
async function loadWorkoutPlans() {
    try {
        const response = await fetch(`${window.API_URL}/workout-plans?user_id=${USER_ID}`);
        const data = await response.json();
        workoutPlans = data.data;
        renderWeeklyPlan();
    } catch (error) {
        console.error('Erro ao carregar planos:', error);
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
                    <div class="workout-title" style="font-size: 1rem; margin-bottom: 0.5rem;">${plan.workout.name}</div>
                    <span class="card-badge">${plan.workout.focus}</span>
                    <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--gray);">
                        ${plan.workout.exercises ? plan.workout.exercises.length + ' exercícios' : ''}
                    </div>
                    <div class="card-actions" style="margin-top: 1rem;">
                        <button class="btn btn-danger btn-small" onclick="deletePlan(${plan.id})">Remover</button>
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

async function createWorkoutPlan(data) {
    try {
        const response = await fetch(`${window.API_URL}/workout-plans`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Treino adicionado ao plano!');
            await loadWorkoutPlans();
            closeModal('planModal');
        } else {
            showError('Erro ao adicionar ao plano');
        }
    } catch (error) {
        showError('Erro ao adicionar ao plano');
        console.error(error);
    }
}

async function deletePlan(id) {
    if (!confirm('Remover este treino do plano semanal?')) return;

    try {
        const response = await fetch(`${window.API_URL}/workout-plans/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Treino removido do plano!');
            await loadWorkoutPlans();
        }
    } catch (error) {
        showError('Erro ao remover do plano');
        console.error(error);
    }
}

// Forms
function setupForms() {
    // Exercise Form
    document.getElementById('exerciseForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const data = {
            name: document.getElementById('exerciseName').value,
            description: document.getElementById('exerciseDescription').value,
            muscle_group: document.getElementById('exerciseMuscleGroup').value,
            equipment: document.getElementById('exerciseEquipment').value,
        };

        await createExercise(data);
    });

    // Workout Form
    document.getElementById('workoutForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (selectedExercisesForWorkout.length === 0) {
            showError('Adicione pelo menos um exercício ao treino');
            return;
        }

        const data = {
            user_id: USER_ID,
            name: document.getElementById('workoutName').value,
            description: document.getElementById('workoutDescription').value,
            focus: document.getElementById('workoutFocus').value,
            exercises: selectedExercisesForWorkout
        };

        await createWorkout(data);
    });

    // Plan Form
    document.getElementById('planForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const time = document.getElementById('planTime').value;
        const data = {
            user_id: USER_ID,
            workout_id: parseInt(document.getElementById('planWorkout').value),
            day_of_week: document.getElementById('planDay').value,
            scheduled_time: time || null,
            is_active: true
        };

        await createWorkoutPlan(data);
    });
}

// Modal Functions
function showAddExerciseModal() {
    document.getElementById('exerciseForm').reset();
    showModal('exerciseModal');
}

function showCreateWorkoutModal() {
    document.getElementById('workoutForm').reset();
    selectedExercisesForWorkout = [];
    document.getElementById('selectedExercises').innerHTML = '';
    showModal('workoutModal');
}

function showAddPlanModal() {
    document.getElementById('planForm').reset();
    showModal('planModal');
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Workout Exercise Functions
function populateExerciseSelect() {
    const select = document.getElementById('exerciseSelect');
    select.innerHTML = '<option value="">Selecione um exercício...</option>';
    
    exercises.forEach(exercise => {
        const option = document.createElement('option');
        option.value = exercise.id;
        option.textContent = `${exercise.name} (${exercise.muscle_group})`;
        select.appendChild(option);
    });
}

function populateWorkoutSelect() {
    const select = document.getElementById('planWorkout');
    select.innerHTML = '<option value="">Selecione um treino...</option>';
    
    workouts.forEach(workout => {
        const option = document.createElement('option');
        option.value = workout.id;
        option.textContent = `${workout.name} - ${workout.focus}`;
        select.appendChild(option);
    });
}

function addExerciseToWorkout() {
    const select = document.getElementById('exerciseSelect');
    const exerciseId = parseInt(select.value);
    
    if (!exerciseId) {
        showError('Selecione um exercício');
        return;
    }

    const exercise = exercises.find(e => e.id === exerciseId);
    if (!exercise) return;

    // Verificar se já foi adicionado
    if (selectedExercisesForWorkout.find(e => e.exercise_id === exerciseId)) {
        showError('Este exercício já foi adicionado');
        return;
    }

    const order = selectedExercisesForWorkout.length + 1;
    selectedExercisesForWorkout.push({
        exercise_id: exerciseId,
        exercise_name: exercise.name,
        order: order,
        sets: 3,
        reps: '10',
        weight: null,
        rest_time: 60,
        notes: null
    });

    renderSelectedExercises();
    select.value = '';
}

function renderSelectedExercises() {
    const container = document.getElementById('selectedExercises');
    
    container.innerHTML = selectedExercisesForWorkout.map((ex, index) => `
        <div class="selected-exercise">
            <div class="selected-exercise-header">
                <span class="selected-exercise-name">${index + 1}. ${ex.exercise_name}</span>
                <button type="button" class="btn btn-danger btn-small" onclick="removeExerciseFromWorkout(${index})">✕</button>
            </div>
            <div class="exercise-details">
                <input type="number" placeholder="Séries" value="${ex.sets}" 
                    onchange="updateExerciseDetail(${index}, 'sets', this.value)" min="1" required>
                <input type="text" placeholder="Reps" value="${ex.reps}" 
                    onchange="updateExerciseDetail(${index}, 'reps', this.value)" required>
                <input type="number" placeholder="Carga (kg)" value="${ex.weight || ''}" 
                    onchange="updateExerciseDetail(${index}, 'weight', this.value)" step="0.5">
                <input type="number" placeholder="Descanso (s)" value="${ex.rest_time || ''}" 
                    onchange="updateExerciseDetail(${index}, 'rest_time', this.value)">
                <input type="text" placeholder="Observações" value="${ex.notes || ''}" 
                    onchange="updateExerciseDetail(${index}, 'notes', this.value)" 
                    style="grid-column: span 2;">
            </div>
        </div>
    `).join('');
}

function updateExerciseDetail(index, field, value) {
    if (field === 'sets' || field === 'rest_time') {
        selectedExercisesForWorkout[index][field] = parseInt(value) || null;
    } else if (field === 'weight') {
        selectedExercisesForWorkout[index][field] = parseFloat(value) || null;
    } else {
        selectedExercisesForWorkout[index][field] = value || null;
    }
}

function removeExerciseFromWorkout(index) {
    selectedExercisesForWorkout.splice(index, 1);
    // Reordenar
    selectedExercisesForWorkout.forEach((ex, i) => {
        ex.order = i + 1;
    });
    renderSelectedExercises();
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

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
