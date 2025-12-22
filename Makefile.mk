.PHONY: help build up down restart logs logs-backend logs-frontend bash-backend bash-frontend composer npm test seed fresh migrate clear-cache key-generate

help:
	@echo "Comandos disponibles:"
	@echo "  build         - Construir imágenes Docker"
	@echo "  up            - Iniciar contenedores"
	@echo "  down          - Detener contenedores"
	@echo "  restart       - Reiniciar contenedores"
	@echo "  logs          - Ver logs de todos los servicios"
	@echo "  logs-backend  - Ver logs del backend"
	@echo "  logs-frontend - Ver logs del frontend"
	@echo "  bash-backend  - Acceder a shell del backend"
	@echo "  bash-frontend - Acceder a shell del frontend"
	@echo "  composer      - Ejecutar comando Composer en backend"
	@echo "  artisan       - Ejecutar comando Artisan"
	@echo "  test          - Ejecutar tests"
	@echo "  seed          - Ejecutar seeders"
	@echo "  fresh         - Ejecutar migrate:fresh --seed"
	@echo "  migrate       - Ejecutar migraciones"
	@echo "  clear-cache   - Limpiar cache de Laravel"
	@echo "  key-generate  - Generar nueva APP_KEY"

build:
	docker-compose build --no-cache

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose restart

logs:
	docker-compose logs -f

logs-backend:
	docker-compose logs -f backend nginx

logs-frontend:
	docker-compose logs -f frontend

bash-backend:
	docker-compose exec backend sh

bash-frontend:
	docker-compose exec frontend sh

composer:
	docker-compose exec backend composer $(filter-out $@,$(MAKECMDGOALS))

artisan:
	docker-compose exec backend php artisan $(filter-out $@,$(MAKECMDGOALS))

test:
	docker-compose exec backend php artisan test

test-coverage:
	docker-compose exec backend php artisan test --coverage

seed:
	docker-compose exec backend php artisan db:seed

fresh:
	docker-compose exec backend php artisan migrate:fresh --seed

migrate:
	docker-compose exec backend php artisan migrate

clear-cache:
	docker-compose exec backend php artisan optimize:clear

key-generate:
	docker-compose exec backend php artisan key:generate

%:
	@: