#!/bin/bash

# Ejecutar migraciones primero
php artisan migrate --force

# Instalar Passport
php artisan passport:install --force

# Generar documentación (si scribe está instalado)
php artisan scribe:generate 2>/dev/null || true

# Iniciar servicios
php-fpm -D
nginx -g "daemon off;"
