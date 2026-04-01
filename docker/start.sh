#!/bin/bash

# Ejecutar migraciones primero
php artisan migrate --force

# Instalar Passport
php artisan passport:install --force

# Iniciar PHP-FPM 
php-fpm -D

# Iniciar Nginx
nginx -g "daemon off;"
