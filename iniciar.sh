#!/bin/bash

echo "🚀 Inicializando proyecto Urbano Express..."

# Verificar que Docker y Docker Compose estén instalados
if ! command -v docker &> /dev/null; then
    echo "❌ Docker no está instalado. Por favor instala Docker primero."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose no está instalado. Por favor instala Docker Compose primero."
    exit 1
fi

echo "✅ Docker y Docker Compose verificados"

# Construir imágenes
echo "🔨 Construyendo imágenes Docker..."
docker-compose build

# Iniciar contenedores
echo "🚢 Iniciando contenedores..."
docker-compose up -d

# Esperar a que los servicios estén listos
echo "⏳ Esperando a que los servicios estén listos..."
sleep 10

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
docker-compose exec backend php artisan key:generate

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
docker-compose exec backend php artisan migrate

# Ejecutar seeders
echo "🌱 Ejecutando seeders..."
docker-compose exec backend php artisan db:seed

echo ""
echo "========================================="
echo "✅ ¡Proyecto inicializado correctamente!"
echo ""
echo "🌐 Frontend:  http://localhost:8080"
echo "🔧 Backend:   http://localhost:8000"
echo "📡 API:       http://localhost:8000/api/v1/"
echo ""
echo "Comandos útiles:"
echo "  make logs         - Ver logs"
echo "  make bash-backend - Acceder al backend"
echo "  make test         - Ejecutar tests"
echo "========================================="