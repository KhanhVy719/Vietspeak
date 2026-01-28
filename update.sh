#!/bin/bash

echo "=========================================="
echo "🚀 UPDATING VIETSPEAK ON VPS"
echo "=========================================="
echo ""

# 1. Pull code
echo "📥  Pulling latest code from GitHub..."
git pull origin master

# 2. Update dependencies
echo "📦  Updating Composer dependencies..."
docker exec laravel_app composer install --no-interaction --optimize-autoloader

# 3. Migrate database
echo "🗄️  Running database migrations..."
docker exec laravel_app php artisan migrate --force

# 4. Clear caches
echo "🧹  Clearing Laravel caches..."
docker exec laravel_app php artisan config:clear
docker exec laravel_app php artisan cache:clear
docker exec laravel_app php artisan route:clear
docker exec laravel_app php artisan view:clear

# 5. Optimization
echo "⚡  Optimizing..."
docker exec laravel_app php artisan config:cache
docker exec laravel_app php artisan route:cache
docker exec laravel_app php artisan view:cache

echo ""
echo "=========================================="
echo "✅  UPDATE COMPLETE!"
echo "=========================================="
