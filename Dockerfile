FROM php:8.4-apache

# Install ekstensi MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Aktifkan modul mod_rewrite (berguna untuk framework PHP/routing)
RUN a2enmod rewrite

WORKDIR /var/www/html
COPY . .

# Pastikan Apache bisa baca file kita
RUN chown -R www-data:www-data /var/www/html