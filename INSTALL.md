# 📖 Guia de Instalação - FitZone

## 📋 Pré-requisitos

### Backend (Laravel)
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Extensões PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

### Frontend
- Navegador web moderno (Chrome, Firefox, Safari, Edge)
- Servidor web (Apache, Nginx) ou PHP built-in server

---

## 🚀 Instalação do Backend

### 1. Configurar Banco de Dados

Crie um banco de dados MySQL:

```sql
CREATE DATABASE fitzone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configurar Variáveis de Ambiente

O arquivo `.env` já está configurado. Verifique as credenciais do banco de dados:

```bash
cd backend
```

Edite o arquivo `.env` se necessário:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitzone
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

### 3. Instalar Dependências

```bash
# Já foi executado, mas caso precise reinstalar:
composer install
```

### 4. Gerar Application Key

```bash
php artisan key:generate
```

### 5. Executar Migrations

```bash
php artisan migrate
```

Isso criará as seguintes tabelas:
- `users` - Usuários do sistema
- `exercises` - Biblioteca de exercícios
- `workouts` - Treinos personalizados
- `workout_exercises` - Relação treino-exercício
- `workout_plans` - Plano semanal

### 6. Popular Banco de Dados (Seeds)

```bash
php artisan db:seed
```

Isso irá criar:
- 1 usuário padrão (email: `user@fitzone.com`)
- 32 exercícios pré-cadastrados em diversos grupos musculares

### 7. Iniciar Servidor de Desenvolvimento

```bash
php artisan serve
```

O backend estará disponível em: `http://localhost:8000`

---

## 🎨 Instalação do Frontend

### 1. Configurar URL da API

Se necessário, edite o arquivo `frontend/js/app.js` e ajuste a URL da API:

```javascript
const API_URL = 'http://localhost:8000/api/v1';
```

### 2. Servir o Frontend

**Opção 1: PHP Built-in Server**
```bash
cd frontend
php -S localhost:3000
```

**Opção 2: Servidor Web (Apache/Nginx)**

Configure o DocumentRoot para apontar para o diretório `frontend/`.

**Opção 3: Extensão Live Server (VS Code)**

Abra o arquivo `index.html` e use a extensão Live Server.

O frontend estará disponível em: `http://localhost:3000` (ou a porta configurada)

---

## 🔧 Comandos Úteis

### Backend

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rotas da API
php artisan route:list

# Recriar banco de dados (atenção: apaga todos os dados!)
php artisan migrate:fresh --seed

# Executar testes
php artisan test
```

---

## 📡 Endpoints da API

### Exercícios
- `GET /api/v1/exercises` - Listar todos os exercícios
- `GET /api/v1/exercises/{id}` - Detalhes de um exercício
- `POST /api/v1/exercises` - Criar exercício
- `PUT /api/v1/exercises/{id}` - Atualizar exercício
- `DELETE /api/v1/exercises/{id}` - Deletar exercício

### Treinos
- `GET /api/v1/workouts` - Listar treinos do usuário
- `GET /api/v1/workouts/{id}` - Detalhes de um treino
- `POST /api/v1/workouts` - Criar treino
- `PUT /api/v1/workouts/{id}` - Atualizar treino
- `DELETE /api/v1/workouts/{id}` - Deletar treino

### Plano Semanal
- `GET /api/v1/workout-plans` - Listar plano semanal
- `GET /api/v1/workout-plans/{id}` - Detalhes de um plano
- `POST /api/v1/workout-plans` - Adicionar treino ao plano
- `PUT /api/v1/workout-plans/{id}` - Atualizar plano
- `DELETE /api/v1/workout-plans/{id}` - Remover do plano
- `GET /api/v1/workout-plans/day/{dia}` - Plano de um dia específico

---

## 🔐 Autenticação (Futuro)

Atualmente o sistema usa um USER_ID fixo. A autenticação completa com Laravel Sanctum será implementada nas próximas versões.

---

## 🐛 Troubleshooting

### Erro: "could not find driver"

Instale a extensão PHP MySQL:

```bash
# Ubuntu/Debian
sudo apt-get install php-mysql

# Windows (XAMPP/WAMP)
# Descomente extension=pdo_mysql no php.ini
```

### Erro: CORS

Se houver problemas de CORS, verifique se o arquivo `config/cors.php` está configurado corretamente e se o middleware está ativo em `bootstrap/app.php`.

### Erro: Connection Refused

Verifique se:
1. O servidor Laravel está rodando (`php artisan serve`)
2. A URL da API no frontend está correta
3. O banco de dados está acessível

---

## 📱 Uso do Sistema

### 1. Biblioteca de Exercícios
- Visualize exercícios por grupo muscular
- Adicione novos exercícios personalizados
- Busque exercícios por nome

### 2. Criar Treinos
- Dê um nome ao seu treino (Ex: "Treino A - Peito e Tríceps")
- Escolha o foco (Hipertrofia, Força, Resistência)
- Adicione exercícios e defina séries, repetições, carga e descanso

### 3. Plano Semanal
- Adicione treinos aos dias da semana
- Defina horários (opcional)
- Visualize sua rotina semanal completa

---

## 🚀 Próximos Passos

1. Implementar autenticação completa
2. Sistema de acompanhamento de treinos realizados
3. Gráficos de evolução de cargas
4. Histórico de treinos
5. Exportar treinos em PDF
6. Aplicativo mobile

---

## 📞 Suporte

Para dúvidas ou problemas, abra uma issue no repositório do projeto.
