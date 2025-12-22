# Challenge Técnico - Urbano Express

## 🚚 Sistema de Integración E-commerce

### Descripción
Sistema de integración para plataformas e-commerce con el sistema interno de gestión de envíos de Urbano Express.

### 🏗️ Arquitectura del Proyecto
integraciones-urbano-express/
├── backend/ # API Laravel
├── frontend/ # Interfaz web HTML/JS
└── sqlite/ # Scripts de base de datos

### 📋 Requisitos Previos
- PHP v8.4 o superior (Corre con Laravel v12)
- Git

### 🚀 Instalación y Ejecución (No-Docker)

1. **Clonar el repositorio:**
```bash
git clone <tu-repositorio>
cd integraciones-urbano-express
```

2. **Configurar variables de entorno:**
```bash
cp backend/.env.example backend/.env
cp backend/.env.testing.example backend/.env.testing
```

3. **Instalar dependencias de Laravel:**
```bash
cd backend
composer install
```

4. **Ejecutar migraciones:**
```bash
cd backend
php artisan migrate
```

5. **Ejecutar seeder (Opcional)**
```bash
cd backend
php artisan db:seed --class=OrdersTableSeeder
```

6. **Generar key de Laravel:**
```bash
cd backend
php artisan key:generate
php artisan key:generate --env=testing
```

7. **Iniciar servidor:**
```bash
cd backend
php artisan serv
```

## 🌐 Servicios Disponibles
Servicio	URL	Descripción
API Backend	http://localhost:8000/api/v1/	API REST Laravel
Frontend	http://localhost:8000	Interfaz web

**********************************************************************
## Tests (No-Docker)

***CMD/Powershell***
```bash
### Ejecutar todos los tests
php artisan test

### Ejecutar tests específicos
php artisan test --filter OrderApiTest
php artisan test --filter TestDataRoutesTest
php artisan test --filter IntegrationTest
php artisan test --filter FrontendFilenTest
```

**********************************************************************
### 🐳 Dockerización

El proyecto está completamente dockerizado y listo para ejecutar en cualquier entorno.

### Requisitos Previos
- Docker 20.10+
- Docker Compose 2.0+

## Inicio Rápido

***WSL/Linux/Mac***
```bash
# Navegar al proyecto
/mnt/c/ruta/a/integraciones-urbano-express

# Usar script de inicialización
./init.sh
```

***PowerShell***
```bash
# Navegar al proyecto
cd C:\ruta\a\integraciones-urbano-express

# Dar permisos (solo primera vez)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process

# Ejecutar el script
.\init.ps1

# O si se desea saltar la reconstrucción (para desarrollo rápido)
.\init.ps1 -SkipBuild
```

***CMD***
```bash
# Navegar al proyecto
cd C:\ruta\a\integraciones-urbano-express

# Ejecutar el script
init.bat
```

**********************************************************************
## Comandos Útiles (Docker)

### Construir/Reconstruir
docker-compose build --no-cache

### Iniciar
docker-compose up -d

### Probar la API
curl http://localhost:8000/api/v1/health

### Ver logs
docker-compose logs

### Detener todo
docker-compose down

**********************************************************************
## Makefile (necesita tener instalado make en linux)

```bash
### Ver todos los comandos disponibles
make help

### Construir imágenes
make buildmake logs

### Iniciar contenedores
make up

### Detener contenedores
make down

### Ver logs
make logs

### Acceder al shell del backend
make bash-backend

### Ejecutar tests
make test

### Generar clave de aplicación
make key-generate

### Ejecutar migraciones
make migrate

### Ejecutar seeders
make seed

### Limpiar cache
make clear-cache
```

## 🌐 Servicios Disponibles
Servicio	URL	Descripción
API Backend	http://localhost:8000/api/v1/	API REST Laravel
Frontend	http://localhost:8080	Interfaz web


### Estructura contenedores:

┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Frontend  │     │  PHP serv   │     │   Backend   │
│   (8080)    │◄────┤   (8000)    │◄────┤  (PHP-FPM)  │
└─────────────┘     └─────────────┘     └─────────────┘
                                           │
                                    ┌──────┴──────┐
                                    │   SQLite    │
                                    │  (archivo)  │
                                    └─────────────┘

**********************************************************************
## Tests (Docker)

***WSL/Linux/MAC/CMD/Powershell***
```bash
### Ejecutar todos los tests
docker exec urbano_api php artisan test

### Ejecutar tests específicos
docker exec urbano_api php artisan test --filter OrderApiTest
docker exec urbano_api php artisan test --filter TestDataRoutesTest
docker exec urbano_api php artisan test --filter IntegrationTest
docker exec urbano_api php artisan test --filter FrontendFilenTest
```

**********************************************************************
## 📡 Endpoints de la API

### POST /api/v1/orders
Crear un nuevo pedido

Request:

json
{
    "order_id": "ORD-2024-001",
    "customer_name": "Juan Pérez",
    "customer_email": "juan@example.com",
    "customer_phone": "+5491123456789",
    "shipping_address": "Calle Falsa 123",
    "shipping_city": "Buenos Aires",
    "shipping_state": "CABA",
    "shipping_zip": "C1405",
    "shipping_country": "Argentina",
    "total_amount": 99.99,
    "currency": "USD",
    "items": [
        {
            "product_id": "PROD-001",
            "name": "Producto 1",
            "quantity": 2,
            "price": 49.99
        }
    ]
}
Response (201):

json
{
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "order_id": "ORD-2024-001",
        "customer_name": "Juan Pérez",
        "status": "pending",
        "created_at": "2024-01-15T10:30:00.000000Z"
    }
}

### GET /api/v1/orders
Obtener todos los pedidos

Response (200):

json
{
    "data": [
        {
            "id": 1,
            "order_id": "ORD-2024-001",
            "customer_name": "Juan Pérez",
            "total_amount": "99.99",
            "status": "pending",
            "created_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "count": 1
}

### GET /api/v1/orders/{id}
Obtener un pedido específico

Response (200):

json
{
    "data": {
        "id": 1,
        "order_id": "ORD-2024-001",
        "customer_name": "Juan Pérez",
        "customer_email": "juan@example.com",
        "shipping_address": "Calle Falsa 123",
        "total_amount": "99.99",
        "status": "pending",
        "items": [
            {
                "product_id": "PROD-001",
                "name": "Producto 1",
                "quantity": 2,
                "price": 49.99
            }
        ],
        "created_at": "2024-01-15T10:30:00.000000Z"
    }
}

### GET /api/v1/health
Health check del servicio

Response (200):

json
{
    "status": "ok",
    "timestamp": "2024-01-15T10:30:00.000000Z",
    "service": "Urbano Express Integration API"
}

### POST /api/v1/test-data/generate
Crea 5 ordenes de prueba (con prefijo "TEST-")

Response (200):

json
{
    "message": "5 órdenes de prueba generadas exitosamente",
    "total_orders": 15
}

### DELETE /api/v1/test-data/clean
Elimina todas las ordenes test (con prefijo "TEST-")

Response (200):

json
{
    "message": "Datos de prueba eliminados",
    "deleted_count": 5,
    "remaining_orders": 10
}
