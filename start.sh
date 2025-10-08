#!/bin/bash

echo "🏋️ FitZone - Iniciando aplicação..."
echo ""

# Verificar se o backend está configurado
if [ ! -f "backend/.env" ]; then
    echo "❌ Erro: Arquivo .env não encontrado no backend"
    echo "Execute a instalação primeiro (veja INSTALL.md)"
    exit 1
fi

# Iniciar backend
echo "📦 Iniciando backend Laravel..."
cd backend
php artisan serve --port=8000 &
BACKEND_PID=$!
echo "✅ Backend rodando em: http://localhost:8000"
echo ""

# Aguardar backend iniciar
sleep 2

# Iniciar frontend
echo "🎨 Iniciando frontend..."
cd ../frontend
php -S localhost:3000 &
FRONTEND_PID=$!
echo "✅ Frontend rodando em: http://localhost:3000"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 FitZone está rodando!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📱 Frontend: http://localhost:3000"
echo "📡 Backend API: http://localhost:8000/api/v1"
echo ""
echo "Pressione Ctrl+C para parar os servidores"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Aguardar Ctrl+C
trap "kill $BACKEND_PID $FRONTEND_PID; echo ''; echo '👋 Servidores encerrados.'; exit" INT
wait
