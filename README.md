# 🏋️‍♂️ FitZone

**FitZone** é uma aplicação web completa desenvolvida para organizar treinos de forma prática e intuitiva. O objetivo é permitir que usuários planejem e acompanhem sua rotina semanal de treinos, separando por dias e grupos musculares.

## 🚀 Tecnologias

### Backend
- **Laravel 12** - Framework PHP
- **MySQL** - Banco de dados
- **API RESTful** - Arquitetura de comunicação

### Frontend  
- **HTML5/CSS3** - Estrutura e estilo
- **JavaScript (Vanilla)** - Interatividade
- **Design Responsivo** - Mobile-first

## 📋 Funcionalidades

### 🔐 Sistema de Autenticação
- ✅ Login com email e senha
- ✅ Dois tipos de usuário: **Personal** e **Cliente**
- ✅ Autenticação com Laravel Sanctum
- ✅ Middleware de proteção de rotas

### 👨‍💼 Para Personal Trainer
- ✅ Gerenciamento completo de clientes
- ✅ Criação e gestão de exercícios
- ✅ Montagem de treinos personalizados
- ✅ Atribuição de treinos aos clientes
- ✅ Visualização de progresso dos clientes
- ✅ Estatísticas de treinos realizados

### 👤 Para Cliente
- ✅ Visualização do plano semanal
- ✅ **Checklist interativo de exercícios**
- ✅ **Sistema de Check-in** (salvar progresso parcial)
- ✅ Marcar treinos como concluídos
- ✅ Sistema de avaliação (1-5 estrelas)
- ✅ Histórico completo de treinos
- ✅ Estatísticas pessoais
- ✅ **Recuperação automática de progresso**

### 🎯 Recursos Gerais
- ✅ API RESTful completa
- ✅ Interface moderna e responsiva
- ✅ Filtro por grupo muscular
- ✅ Busca de exercícios
- ✅ Biblioteca com 32 exercícios pré-cadastrados

## 📂 Estrutura do Projeto

```
Fitzone/
├── backend/          # API Laravel
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
├── frontend/         # Site HTML/CSS/JS
│   ├── css/
│   ├── js/
│   └── index.html
└── README.md
```

## 🔧 Instalação e Uso

### 📖 Documentação Disponível:
- **[INSTALL.md](INSTALL.md)** - Guia completo de instalação
- **[SISTEMA-AUTH.md](SISTEMA-AUTH.md)** - Sistema de autenticação e roles
- **[COMO-FUNCIONA-CHECKIN.md](COMO-FUNCIONA-CHECKIN.md)** - Sistema de check-in de exercícios
- **[GUIA-DE-USO.md](GUIA-DE-USO.md)** - Manual completo para Personal e Cliente
- **[API_DOCS.md](API_DOCS.md)** - Documentação completa da API
- **[database.sql](database.sql)** - Script SQL do banco de dados

### ⚡ Início Rápido:

```bash
# 1. Criar banco de dados
mysql -u root -p
CREATE DATABASE fitzone;
exit;

# 2. Configurar backend
cd backend
php artisan migrate:fresh --seed

# 3. Iniciar servidores
# Terminal 1:
php artisan serve

# Terminal 2:
cd ../frontend
npm run dev
```

### 🌐 Acessar:
- **Frontend:** http://localhost:3000/login.html
- **Backend API:** http://localhost:8000/api/v1

### 👥 Usuários Demo:
| Tipo | Email | Senha |
|------|-------|-------|
| **Personal** | personal@fitzone.com | password |
| **Cliente 1** | joao@fitzone.com | password |
| **Cliente 2** | maria@fitzone.com | password |
