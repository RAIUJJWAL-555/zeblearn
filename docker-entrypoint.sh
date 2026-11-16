#!/bin/bash
set -e

# Configure port for Apache (Render provides PORT, defaults to 10000)
PORT=${PORT:-10000}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# 1. Ensure APP_KEY is present so EncryptCookies never throws MissingAppKeyException
if [ -z "$APP_KEY" ]; then
    echo "[Entrypoint] APP_KEY is not set. Generating application encryption key..."
    php artisan key:generate --force
fi

# 2. Ensure SQLite database file exists and is writable if using sqlite connection
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p database
    touch database/database.sqlite
    chown -R www-data:www-data database
    chmod -R ug+rwx database
fi

# 3. Ensure runtime storage and bootstrap/cache directories exist with proper permissions
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

# 4. Cache configuration, routes, and views for production performance
echo "[Entrypoint] Caching application configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run database migrations safely (non-destructive)
echo "[Entrypoint] Running database migrations..."
php artisan migrate --force --isolated || echo "[Entrypoint] Notice: Migration encountered an issue, proceeding..."

# 6. Seed essential course and trust records if needed (idempotent updateOrCreate)
echo "[Entrypoint] Seeding course and trust proof records..."
php artisan db:seed --class=CourseSeeder --force || true
php artisan db:seed --class=TrustProofSeeder --force || true

# 7. Final ownership check for web server user
chown -R www-data:www-data storage bootstrap/cache database

echo "[Entrypoint] Starting Apache on port ${PORT}..."
exec apache2-foreground

