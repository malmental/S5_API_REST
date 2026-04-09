#!/bin/bash

# Only generate APP_KEY if not already set in environment
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Start PHP-FPM immediately (don't wait for MySQL)
php-fpm -D

# Start Nginx immediately (don't wait for MySQL)
nginx -g "daemon off;"
