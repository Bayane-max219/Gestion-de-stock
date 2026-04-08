#!/bin/sh
set -e

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  SmartERP Pro — Laravel API Starting..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Attendre que MySQL soit prêt
echo "⏳ Waiting for database..."
until php -r "
try {
    new PDO(
        'mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}',
        '${DB_USERNAME}',
        '${DB_PASSWORD}'
    );
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
"; do
    echo "   Database not ready yet, retrying in 3s..."
    sleep 3
done
echo "✅ Database connected!"

# Migrations
echo "📦 Running migrations..."
php artisan migrate --force --no-interaction

# Seeder si première installation
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "🌱 Running seeders..."
    php artisan db:seed --force --no-interaction
fi

# Lier le storage public
php artisan storage:link --force 2>/dev/null || true

# Vider les caches pour Docker
php artisan config:cache
php artisan route:cache

echo "🚀 SmartERP Pro API is ready!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Démarrer PHP-FPM
exec "$@"
