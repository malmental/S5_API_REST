FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Configure PHP-FPM to listen on port 9000
RUN sed -i 's/listen = .*/listen = 0.0.0.0:9000/' /usr/local/etc/php-fpm.d/www.conf

COPY docker/nginx.conf /etc/nginx/sites-available/default

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
