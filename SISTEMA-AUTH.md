# 🔐 Sistema de Autenticação - FitZone

## 📋 Visão Geral

O FitZone agora possui um sistema completo de autenticação com **dois tipos de usuários**:

### 👤 Personal Trainer
- Gerencia clientes
- Cria exercícios e treinos
- Atribui treinos aos clientes
- Visualiza progresso dos clientes

### 👥 Cliente
- Visualiza plano semanal de treinos
- Marca treinos como concluídos
- Avalia treinos realizados
- Acompanha histórico e estatísticas

---

## 🚀 Como Usar

### 1. Iniciar os Servidores

```bash
# Backend (Terminal 1)
cd backend
php artisan serve

# Frontend (Terminal 2)
cd frontend
npm run dev
```

### 2. Acessar o Sistema

Abra: **http://localhost:3000/login.html**

---

## 👥 Usuários de Demonstração

### Personal Trainer
```
Email: personal@fitzone.com
Senha: password
```

**O que pode fazer:**
- ✅ Criar e gerenciar clientes
- ✅ Criar exercícios personalizados
- ✅ Montar treinos
- ✅ Atribuir treinos para clientes
- ✅ Visualizar progresso dos clientes

### Clientes
```
Cliente 1:
Email: joao@fitzone.com
Senha: password

Cliente 2:
Email: maria@fitzone.com
Senha: password
```

**O que podem fazer:**
- ✅ Ver plano semanal de treinos
- ✅ Marcar treinos como concluídos
- ✅ Avaliar treinos (1-5 estrelas)
- ✅ Adicionar observações
- ✅ Ver histórico de treinos
- ✅ Ver estatísticas pessoais

---

## 📱 Fluxo de Uso

### Para o Personal Trainer:

1. **Login** em `http://localhost:3000/login.html`
2. Acesse o **Dashboard do Personal**
3. **Crie clientes** na aba "Meus Clientes"
4. **Crie exercícios** na biblioteca
5. **Monte treinos** com os exercícios
6. **Atribua treinos** aos clientes no plano semanal

### Para o Cliente:

1. **Login** em `http://localhost:3000/login.html`
2. Acesse o **Dashboard do Cliente**
3. Veja seu **plano semanal** de treinos
4. Clique em **"Marcar como Feito"** após realizar o treino
5. **Avalie** o treino e adicione observações
6. Acompanhe seu **histórico** e **estatísticas**

---

## 🗄️ Estrutura do Banco de Dados

### Novas Tabelas:

**1. Modificação em `users`:**
```sql
- role: enum('personal', 'cliente')
- personal_id: FK para users (personal do cliente)
- phone: telefone
- bio: biografia
- avatar: foto de perfil
```

**2. `personal_clients`:**
- Relacionamento many-to-many entre personal e clientes
- Status (ativo/inativo/pendente)
- Objetivos do cliente
- Observações do personal

**3. `workout_logs`:**
- Registro de treinos realizados
- Data de conclusão
- Duração em minutos
- Avaliação (1-5 estrelas)
- Observações do cliente

---

## 🔐 API Endpoints

### Autenticação

```http
POST /api/v1/register - Registrar novo usuário
POST /api/v1/login - Login
POST /api/v1/logout - Logout
GET /api/v1/me - Dados do usuário autenticado
```

### Clientes (Personal apenas)

```http
GET /api/v1/clients - Listar clientes
POST /api/v1/clients - Criar cliente
GET /api/v1/clients/{id} - Detalhes do cliente
PUT /api/v1/clients/{id} - Atualizar cliente
DELETE /api/v1/clients/{id} - Desativar cliente
```

### Workout Logs

```http
GET /api/v1/workout-logs - Listar histórico
POST /api/v1/workout-logs - Registrar treino
GET /api/v1/workout-logs-stats - Estatísticas
PUT /api/v1/workout-logs/{id} - Atualizar log
DELETE /api/v1/workout-logs/{id} - Deletar log
```

### Autenticação na API

Todas as requisições protegidas precisam do header:
```
Authorization: Bearer {token}
```

---

## 🎨 Páginas do Frontend

### Públicas:
- `login.html` - Tela de login
- `register.html` - Cadastro (a implementar)

### Protegidas:
- `dashboard-personal.html` - Dashboard do Personal
- `dashboard-cliente.html` - Dashboard do Cliente
- `index.html` - Sistema original (biblioteca de exercícios)

---

## 🔄 Fluxo de Autenticação

1. Usuário faz login
2. Backend retorna token JWT (Laravel Sanctum)
3. Token é salvo no localStorage
4. Todas as requisições incluem o token no header
5. Backend valida o token e retorna dados

---

## 🛠️ Tecnologias Usadas

### Backend:
- Laravel 12
- Laravel Sanctum (autenticação)
- MySQL
- Middleware de Role

### Frontend:
- HTML5 / CSS3 / JavaScript Vanilla
- LocalStorage para token
- Fetch API
- Browser-Sync (hot reload)

---

## 📊 Recursos Implementados

✅ Sistema de login/logout  
✅ Autenticação com token (Sanctum)  
✅ Middleware de role (personal/cliente)  
✅ Dashboard do Personal  
✅ Dashboard do Cliente  
✅ Gestão de clientes  
✅ Registro de treinos realizados  
✅ Sistema de avaliação (1-5 estrelas)  
✅ Estatísticas pessoais  
✅ Histórico de treinos  
✅ Plano semanal personalizado  

---

## 🚧 Próximas Funcionalidades

- [ ] Tela de registro (cadastro público)
- [ ] Redefinição de senha
- [ ] Upload de foto de perfil
- [ ] Gráficos de evolução
- [ ] Notificações push
- [ ] Chat personal-cliente
- [ ] Pagamentos integrados
- [ ] Aplicativo mobile

---

## 🐛 Troubleshooting

### Token inválido / Não autorizado

1. Faça logout
2. Limpe o localStorage do navegador
3. Faça login novamente

### Erro CORS

Verifique se ambos servidores estão rodando:
- Backend: `http://localhost:8000`
- Frontend: `http://localhost:3000`

### Banco de dados

Se precisar recriar:
```bash
cd backend
php artisan migrate:fresh --seed
```

---

## 📞 Suporte

Para dúvidas ou problemas, consulte:
- `INSTALL.md` - Instalação
- `API_DOCS.md` - Documentação da API
- `README.md` - Visão geral do projeto
