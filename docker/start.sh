#!/bin/bash

# Ejecutar migraciones primero
php artisan migrate --force

# Instalar Passport
php artisan passport:install --force

# Generar documentación (si scribe está instalado)
php artisan scribe:generate 2>/dev/null || true

# Iniciar PHP-FPM con socket Unix
php-fpm --fpm-config /usr/local/etc/php-fpm.d/www.conf -D

# Iniciar Nginx
nginx -g "daemon off;"
