#!/bin/bash

# Only generate APP_KEY if not already set in environment
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Wait for MySQL to be ready before running migrations
echo "Waiting for MySQL to be ready..."
until php artisan migrate --force 2>/dev/null; do
    echo "MySQL not ready, waiting..."
    sleep 3
done

echo "MySQL is ready!"

# Install Passport (only if migrations succeeded)
php artisan passport:install --force

# Start PHP-FPM 
php-fpm -D

# Start Nginx
nginx -g "daemon off;"
