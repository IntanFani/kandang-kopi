FROM php:8.2-apache

# 1. Install sistem dependensi (termasuk libzip-dev untuk ekstensi zip)
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# 2. Aktifkan Apache rewrite
RUN a2enmod rewrite

# 3. Set working directory
WORKDIR /var/www/html

# 4. Copy seluruh file proyek
COPY . .

# 5. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# 7. Atur Permission folder storage dan cache
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80