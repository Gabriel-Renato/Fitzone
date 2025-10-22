# Configuração do Banco de Dados para Vercel

⚠️ **IMPORTANTE**: O Vercel NÃO oferece banco de dados próprio. Você precisa usar um serviço externo.

## 🏆 Opções Recomendadas (em ordem de preferência):

### 1. **PlanetScale** ⭐ (MAIS RECOMENDADO)
- ✅ **MySQL serverless** (100% compatível com Laravel)
- ✅ **Gratuito** até 1GB
- ✅ **Escalável** automaticamente
- ✅ **Backup automático**
- ✅ **Interface web** super fácil
- 🔗 [planetscale.com](https://planetscale.com)

**Como configurar:**
1. Crie conta no PlanetScale
2. Crie um novo banco
3. Obtenha a string de conexão
4. Configure no Vercel

### 2. **Railway** 🚂
- ✅ **MySQL nativo**
- ✅ **Deploy automático**
- ✅ **Interface simples**
- ✅ **Plano gratuito**
- 🔗 [railway.app](https://railway.app)

### 3. **Neon** 🌟 (PostgreSQL)
- ✅ **PostgreSQL serverless**
- ✅ **Muito rápido**
- ✅ **Gratuito** até 3GB
- ⚠️ Requer ajuste no Laravel (trocar MySQL por PostgreSQL)
- 🔗 [neon.tech](https://neon.tech)

### 4. **Supabase** 🔥 (PostgreSQL)
- ✅ **PostgreSQL** com interface web
- ✅ **Auth integrado**
- ✅ **Gratuito** até 500MB
- ⚠️ Requer ajuste no Laravel
- 🔗 [supabase.com](https://supabase.com)

## Configuração no Vercel:

1. Acesse o dashboard do Vercel
2. Vá em Settings > Environment Variables
3. Adicione as seguintes variáveis:

```
DB_CONNECTION=mysql
DB_HOST=seu-host-aqui
DB_PORT=3306
DB_DATABASE=nome-do-banco
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```

## Para usar SQLite (desenvolvimento/teste):
```
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
```

## Migrations:
Após configurar o banco, execute as migrations:
```bash
php artisan migrate --force
```

## Seeders (se necessário):
```bash
php artisan db:seed --force
```
