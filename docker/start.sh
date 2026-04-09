#!/bin/bash

# Only generate APP_KEY if not already set in environment
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Install Passport
php artisan passport:install --force

# Start PHP-FPM 
php-fpm -D

# Start Nginx
nginx -g "daemon off;"
