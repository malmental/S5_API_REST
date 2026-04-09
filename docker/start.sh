#!/bin/bash

# Generate APP_KEY if not set
php artisan key:generate --force

# Ejecutar migraciones primero
php artisan migrate --force

# Instalar Passport
php artisan passport:install --force

# Iniciar PHP-FPM 
php-fpm -D

# Iniciar Nginx
nginx -g "daemon off;"
