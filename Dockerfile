# Minimal production-ready Dockerfile for Laravel (PHP-FPM)

# 1) Base PHP with needed extensions
FROM php:8.2-fpm-alpine AS base
RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-libs \
    && docker-php-ext-install intl pdo_mysql pdo_sqlite mbstring zip
WORKDIR /var/www/html

# 2) Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# 3) Final stage
FROM base AS final
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .

# Optimize Laravel cache (config/routes/views). Comment out if not desired during build.
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache || true

# PHP-FPM listens on 9000 by default
EXPOSE 9000
CMD ["php-fpm"]
