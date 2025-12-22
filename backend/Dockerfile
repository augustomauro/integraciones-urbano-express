# Dockerfile extremadamente simple
FROM php:8.4-cli-alpine

RUN apk add --no-cache sqlite sqlite-dev \
    && docker-php-ext-install pdo pdo_sqlite

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts

COPY . .

RUN if [ ! -f .env ]; then \
    cp .env.example .env && \
    php artisan key:generate --force; \
    fi

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
