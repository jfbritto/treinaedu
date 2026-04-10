#!/bin/bash
set -e

cd /var/www/treinaedu

echo ">> Entering maintenance mode..."
php artisan down --retry=15 --refresh=5 2>/dev/null || true

echo ">> Resetting local changes..."
git checkout -- .

echo ">> Pulling latest code..."
git pull origin main

echo ">> Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ">> Running migrations..."
php artisan migrate --force

echo ">> Clearing and caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo ">> Restarting queue workers..."
sudo supervisorctl restart treinaedu-worker:*

echo ">> Exiting maintenance mode..."
php artisan up

echo ">> Deploy complete!"
