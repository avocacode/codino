FROM php:8.4-apache

# Install ekstensi MySQL dan utilitas pendukung (unzip dibutuhkan oleh composer)
RUN apt-get update && apt-get install -y unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Ambil Composer resmi dari image Docker Composer dan salin ke dalam PHP kita
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html