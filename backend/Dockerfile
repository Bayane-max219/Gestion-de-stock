# ─────────────────────────────────────────────────────────────────────────────
# Stage 1 : Build — installer les dépendances PHP (sans dev)
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS builder

WORKDIR /var/www/html

# Installer les extensions PHP nécessaires
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    oniguruma-dev \
    mysql-client \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        xml

# Installer Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copier les fichiers de dépendances en premier (cache layer)
COPY composer.json composer.lock ./

# Installer les dépendances de production uniquement
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier le reste du code source
COPY . .

# Générer la clé et optimiser
RUN php artisan key:generate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Permissions du storage et cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ─────────────────────────────────────────────────────────────────────────────
# Stage 2 : Production image
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine AS production

WORKDIR /var/www/html

# Dépendances runtime seulement
RUN apk add --no-cache \
    libpng \
    libxml2 \
    oniguruma \
    mysql-client \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        xml

# Copier depuis le builder
COPY --from=builder /var/www/html /var/www/html

# Configuration PHP-FPM
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Script d'entrée (migrations + start)
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
