# 📚 Documentação da API - FitZone

Base URL: `http://localhost:8000/api/v1`

Todas as respostas seguem o padrão:
```json
{
    "success": true/false,
    "message": "Mensagem de sucesso/erro",
    "data": {} // Dados retornados
}
```

---

## 🏋️ Exercises (Exercícios)

### Listar todos os exercícios

```http
GET /exercises
```

**Query Parameters:**
- `muscle_group` (opcional) - Filtrar por grupo muscular
- `search` (opcional) - Buscar por nome

**Resposta de sucesso:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Supino Reto",
            "description": "Exercício básico para desenvolvimento do peitoral",
            "muscle_group": "Peito",
            "equipment": "Barra/Halteres",
            "video_url": null,
            "image_url": null,
            "created_at": "2025-10-08T12:00:00.000000Z",
            "updated_at": "2025-10-08T12:00:00.000000Z"
        }
    ]
}
```

---

### Obter um exercício específico

```http
GET /exercises/{id}
```

**Resposta de sucesso:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Supino Reto",
        "description": "Exercício básico para desenvolvimento do peitoral",
        "muscle_group": "Peito",
        "equipment": "Barra/Halteres",
        "video_url": null,
        "image_url": null
    }
}
```

---

### Criar um novo exercício

```http
POST /exercises
```

**Body:**
```json
{
    "name": "Nome do Exercício",
    "description": "Descrição opcional",
    "muscle_group": "Grupo Muscular",
    "equipment": "Equipamento necessário",
    "video_url": "https://...",
    "image_url": "https://..."
}
```

**Campos obrigatórios:**
- `name` (string, max: 255)
- `muscle_group` (string, max: 255)

**Resposta de sucesso:**
```json
{
    "success": true,
    "message": "Exercício criado com sucesso",
    "data": { ... }
}
```

---

### Atualizar um exercício

```http
PUT /exercises/{id}
```

**Body:** Mesmos campos do POST (todos opcionais)

---

### Deletar um exercício

```http
DELETE /exercises/{id}
```

**Resposta de sucesso:**
```json
{
    "success": true,
    "message": "Exercício deletado com sucesso"
}
```

---

### Obter grupos musculares

```http
GET /exercises/muscle-groups
```

**Resposta:**
```json
{
    "success": true,
    "data": ["Peito", "Costas", "Pernas", "Ombros", "Bíceps", "Tríceps", "Abdômen"]
}
```

---

## 💪 Workouts (Treinos)

### Listar treinos do usuário

```http
GET /workouts?user_id={user_id}
```

**Resposta de sucesso:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "name": "Treino A - Peito e Tríceps",
            "description": "Treino focado em hipertrofia",
            "focus": "Hipertrofia",
            "created_at": "2025-10-08T12:00:00.000000Z",
            "exercises": [
                {
                    "id": 1,
                    "name": "Supino Reto",
                    "muscle_group": "Peito",
                    "pivot": {
                        "workout_id": 1,
                        "exercise_id": 1,
                        "order": 1,
                        "sets": 4,
                        "reps": "8-10",
                        "weight": 80.00,
                        "rest_time": 90,
                        "notes": "Manter forma correta"
                    }
                }
            ]
        }
    ]
}
```

---

### Obter um treino específico

```http
GET /workouts/{id}
```

---

### Criar um novo treino

```http
POST /workouts
```

**Body:**
```json
{
    "user_id": 1,
    "name": "Treino A - Peito e Tríceps",
    "description": "Descrição opcional",
    "focus": "Hipertrofia",
    "exercises": [
        {
            "exercise_id": 1,
            "order": 1,
            "sets": 4,
            "reps": "8-10",
            "weight": 80.5,
            "rest_time": 90,
            "notes": "Observações opcionais"
        },
        {
            "exercise_id": 2,
            "order": 2,
            "sets": 3,
            "reps": "12",
            "weight": null,
            "rest_time": 60,
            "notes": null
        }
    ]
}
```

**Campos obrigatórios:**
- `user_id` (integer, exists:users)
- `name` (string, max: 255)
- `focus` (string, max: 255)
- `exercises` (array, min: 1)
- `exercises.*.exercise_id` (integer, exists:exercises)
- `exercises.*.order` (integer)
- `exercises.*.sets` (integer, min: 1)
- `exercises.*.reps` (string)

**Resposta de sucesso:**
```json
{
    "success": true,
    "message": "Treino criado com sucesso",
    "data": { ... }
}
```

---

### Atualizar um treino

```http
PUT /workouts/{id}
```

**Body:** Mesmos campos do POST (todos opcionais exceto exercises quando fornecido)

**Nota:** Ao atualizar os exercícios, todos os exercícios antigos serão substituídos pelos novos.

---

### Deletar um treino

```http
DELETE /workouts/{id}
```

---

## 📅 Workout Plans (Plano Semanal)

### Listar plano semanal do usuário

```http
GET /workout-plans?user_id={user_id}
```

**Resposta de sucesso:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "workout_id": 1,
            "day_of_week": "Segunda",
            "scheduled_time": "07:00:00",
            "is_active": true,
            "created_at": "2025-10-08T12:00:00.000000Z",
            "workout": {
                "id": 1,
                "name": "Treino A",
                "focus": "Hipertrofia",
                "exercises": [...]
            }
        }
    ]
}
```

---

### Obter plano de um dia específico

```http
GET /workout-plans/day/{day}?user_id={user_id}
```

**Dias válidos:** Segunda, Terça, Quarta, Quinta, Sexta, Sábado, Domingo

---

### Adicionar treino ao plano

```http
POST /workout-plans
```

**Body:**
```json
{
    "user_id": 1,
    "workout_id": 1,
    "day_of_week": "Segunda",
    "scheduled_time": "07:00",
    "is_active": true
}
```

**Campos obrigatórios:**
- `user_id` (integer, exists:users)
- `workout_id` (integer, exists:workouts)
- `day_of_week` (enum: Segunda, Terça, Quarta, Quinta, Sexta, Sábado, Domingo)

**Campos opcionais:**
- `scheduled_time` (time, format: H:i)
- `is_active` (boolean, default: true)

---

### Atualizar plano

```http
PUT /workout-plans/{id}
```

---

### Remover treino do plano

```http
DELETE /workout-plans/{id}
```

---

## ❌ Códigos de Erro

- `200` - Sucesso
- `201` - Criado com sucesso
- `404` - Recurso não encontrado
- `422` - Erro de validação
- `500` - Erro interno do servidor

**Exemplo de erro de validação:**
```json
{
    "success": false,
    "errors": {
        "name": ["O campo nome é obrigatório."],
        "muscle_group": ["O campo grupo muscular é obrigatório."]
    }
}
```

---

## 🔐 Autenticação (Futuro)

Nas próximas versões, todas as rotas exigirão autenticação via Laravel Sanctum:

```http
Authorization: Bearer {token}
```

---

## 📝 Exemplos de Uso

### Criar um treino completo

```javascript
const response = await fetch('http://localhost:8000/api/v1/workouts', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        user_id: 1,
        name: "Treino A - Peito e Tríceps",
        description: "Foco em hipertrofia",
        focus: "Hipertrofia",
        exercises: [
            {
                exercise_id: 1,
                order: 1,
                sets: 4,
                reps: "8-10",
                weight: 80,
                rest_time: 90,
                notes: "Controlar descida"
            },
            {
                exercise_id: 2,
                order: 2,
                sets: 4,
                reps: "10-12",
                weight: 60,
                rest_time: 90,
                notes: null
            }
        ]
    })
});

const data = await response.json();
console.log(data);
```

---

## 🧪 Testando a API

### Usando cURL

```bash
# Listar exercícios
curl http://localhost:8000/api/v1/exercises

# Criar exercício
curl -X POST http://localhost:8000/api/v1/exercises \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Agachamento Livre",
    "description": "Exercício composto para pernas",
    "muscle_group": "Pernas",
    "equipment": "Barra"
  }'

# Buscar exercícios de peito
curl "http://localhost:8000/api/v1/exercises?muscle_group=Peito"
```

### Usando Postman / Insomnia

Importe a collection com todos os endpoints disponível no arquivo `postman_collection.json` (a ser criado).

---

## 📊 Estrutura do Banco de Dados

### Tabela: exercises
- id (PK)
- name
- description
- muscle_group
- equipment
- video_url
- image_url
- timestamps

### Tabela: workouts
- id (PK)
- user_id (FK)
- name
- description
- focus
- timestamps

### Tabela: workout_exercises
- id (PK)
- workout_id (FK)
- exercise_id (FK)
- order
- sets
- reps
- weight
- rest_time
- notes
- timestamps

### Tabela: workout_plans
- id (PK)
- user_id (FK)
- workout_id (FK)
- day_of_week
- scheduled_time
- is_active
- timestamps
