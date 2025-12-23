#!/bin/bash

# 0. Configurar directorio seguro para Git (evita error de ownership en Windows)
git config --global --add safe.directory /var/www/html

# 1. Si existe vendor pero da error, mejor lo limpiamos para asegurar
if [ -d "vendor" ] && [ ! -f "vendor/laravel/framework/src/Illuminate/Foundation/Application.php" ]; then
    echo "Carpeta vendor corrupta detectada. Limpiando..."
    rm -rf vendor
    rm composer.lock
fi

# 2. Instalación de dependencias
if [ ! -d "vendor" ]; then
    echo "Instalando dependencias desde cero..."
    # Primero instalamos SIN ejecutar scripts de Laravel para evitar el Fatal Error
    composer install --no-interaction --prefer-dist --no-scripts
    
    echo "Generando autoloader completo..."
    composer dump-autoload --optimize
    
    echo "Ejecutando scripts post-instalación..."
    # Ahora que el autoloader existe, ya podemos ejecutar los scripts de Laravel
    composer run-script post-autoload-dump
else
    echo "Dependencias encontradas."
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