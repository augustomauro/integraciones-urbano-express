Write-Host "========================================" -ForegroundColor Cyan
Write-Host "INICIANDO URBANO EXPRESS EN DOCKER" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar Docker
Write-Host "1. Verificando Docker..." -ForegroundColor Yellow
try {
    docker --version | Out-Null
    Write-Host "   ✅ Docker está instalado" -ForegroundColor Green
} catch {
    Write-Host "   ❌ ERROR: Docker no está instalado o no está corriendo" -ForegroundColor Red
    Write-Host "   Instala Docker Desktop desde: https://docker.com" -ForegroundColor Yellow
    pause
    exit
}

# 2. Construir
Write-Host "2. Construyendo contenedores..." -ForegroundColor Yellow
docker-compose build

# 3. Iniciar
Write-Host "3. Iniciando servicios..." -ForegroundColor Yellow
docker-compose up -d

# 4. Configurar Laravel
Write-Host "4. Configurando Laravel..." -ForegroundColor Yellow
docker-compose exec laravel php artisan key:generate
docker-compose exec laravel php artisan migrate
docker-compose exec laravel php artisan db:seed

# 5. Mostrar URLs
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "¡LISTO!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Frontend:  http://localhost:8080" -ForegroundColor Cyan
Write-Host "🔧 Backend:   http://localhost:8000" -ForegroundColor Cyan
Write-Host "📡 API:       http://localhost:8000/api/v1/" -ForegroundColor Cyan
Write-Host ""
Write-Host "Comandos útiles:" -ForegroundColor Yellow
Write-Host "  📋 ver logs:        docker-compose logs" -ForegroundColor Gray
Write-Host "  ⏹️  detener:         docker-compose down" -ForegroundColor Gray
Write-Host "  💻 bash laravel:    docker-compose exec laravel bash" -ForegroundColor Gray
Write-Host "  🧪 tests:           docker-compose exec laravel php artisan test" -ForegroundColor Gray
Write-Host "========================================" -ForegroundColor Green

# Abrir navegador automáticamente
Start-Process "http://localhost:8080"