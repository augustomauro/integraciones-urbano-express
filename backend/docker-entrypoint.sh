#!/bin/bash

# 1. Configurar directorio seguro para Git (evita error de ownership en Windows)
git config --global --add safe.directory /var/www/html

# 2. Instalar dependencias si falta la carpeta vendor
if [ ! -d "vendor" ]; then
    echo "Carpeta vendor no encontrada. Instalando dependencias (esto puede tardar)..."
    # Usamos --no-scripts para que no intente ejecutar nada de Laravel hasta tener el autoload
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts
    
    # Una vez instalado vendor, generamos el autoload real con scripts
    composer dump-autoload
fi

# 3. Preparar la base de datos SQLite
if [ ! -f "database/database.sqlite" ]; then
    echo "Creando base de datos SQLite..."
    touch database/database.sqlite
    chmod 666 database/database.sqlite
fi

# 4. Configurar .env si no existe
if [ ! -f ".env" ]; then
    cp .env.example .env
    # Ahora que vendor existe, podemos usar php artisan
    php artisan key:generate
fi

# 5. Correr migraciones
echo "Corriendo migraciones..."
php artisan migrate --force

echo "Setup finalizado correctamente."
# Salimos del script para que el 'command' del compose siga con 'php artisan serve'
exit 0