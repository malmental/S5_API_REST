# Usar PHP 8.4 con FPM
FROM php:8.4-fpm

# Establecer directorio de trabajo
WORKDIR /var/www

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer (gestor de dependencias PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos de Composer
COPY composer.json composer.lock ./

# Copiar todos los archivos de la aplicación
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Permisos correctos
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/bootstrap/cache

# Exponer puerto 8000
EXPOSE 8000

# Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]