#!/bin/sh

set -e

echo "🚀 Iniciando aplicación Urbano Express..."

# Verificar que el directorio de la aplicación existe
if [ ! -d "/var/www/html" ]; then
    echo "❌ Error: Directorio /var/www/html no encontrado"
    exit 1
fi

cd /var/www/html

# Verificar si es la primera ejecución
if [ ! -f ".env" ]; then
    echo "📋 Configurando entorno por primera vez..."
    
    # Copiar archivo .env si no existe
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ Archivo .env copiado desde .env.example"
    else
        echo "⚠️  Advertencia: .env.example no encontrado"
    fi
fi

# Configurar SQLite si se usa
if [ "$DB_CONNECTION" = "sqlite" ] || [ ! "$DB_CONNECTION" ]; then
    echo "🗃️  Configurando SQLite..."
    
    # Crear directorio de base de datos si no existe
    mkdir -p database
    
    # Crear archivo de base de datos SQLite si no existe
    if [ ! -f "database/database.sqlite" ]; then
        touch database/database.sqlite
        echo "✅ Base de datos SQLite creada"
    fi
    
    # Configurar permisos
    chmod 775 database
    chmod 664 database/database.sqlite
fi

# Instalar dependencias de Composer si node_modules no existe
if [ ! -d "vendor" ] && [ -f "composer.json" ]; then
    echo "📦 Instalando dependencias de Composer..."
    composer install --no-interaction --optimize-autoloader --no-scripts
fi

# Generar key de Laravel si no existe
if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" = "" ]; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --force
fi

# Ejecutar migraciones
if [ -f "database/migrations" ]; then
    echo "🗄️  Ejecutando migraciones..."
    php artisan migrate --force
fi

# Limpiar cache
echo "🧹 Limpiando cache..."
php artisan optimize:clear

echo "✅ Configuración completada"

# Ejecutar el comando principal
exec "$@"