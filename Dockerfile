FROM php:8.4-fpm-alpine

# Install ekstensi MySQL yang dibutuhkan PHP
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory di dalam kontainer
WORKDIR /var/www/html

# Salin seluruh kode proyek ke dalam kontainer
COPY . .

# Set hak akses (opsional, menyesuaikan kebutuhan framework)
RUN chown -R www-data:www-data /var/www/html