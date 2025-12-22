@echo off
echo ========================================
echo INICIANDO URBANO EXPRESS EN DOCKER
echo ========================================
echo.

echo 1. Verificando Docker...
docker --version
if errorlevel 1 (
    echo ERROR: Docker no está instalado o no está corriendo
    echo Instala Docker Desktop desde: https://docker.com
    pause
    exit /b 1
)

echo 2. Construyendo contenedores...
docker-compose build

echo 3. Iniciando servicios...
docker-compose up -d

echo 4. Configurando Laravel...
docker-compose exec laravel php artisan key:generate
docker-compose exec laravel php artisan migrate
docker-compose exec laravel php artisan db:seed

echo.
echo ========================================
echo ¡LISTO!
echo.
echo Frontend:  http://localhost:8080
echo Backend:   http://localhost:8000
echo API:       http://localhost:8000/api/v1/
echo.
echo Comandos útiles:
echo   ver logs:        docker-compose logs
echo   detener:         docker-compose down
echo   bash laravel:    docker-compose exec laravel bash
echo ========================================
pause