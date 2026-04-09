#!/bin/bash

# Create .env file from Railway environment variables
cat > /var/www/.env << EOF
APP_NAME=INCIDENsly
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=${APP_URL}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
EOF

# Run migrations
php artisan migrate --force

# Install Passport
php artisan passport:install --force

# Link storage
php artisan storage:link

# Start PHP-FPM
php-fpm -D

# Start Nginx with custom config
nginx -c /docker/nginx.conf -g "daemon off;"