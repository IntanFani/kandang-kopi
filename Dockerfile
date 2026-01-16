FROM php:8.2-apache

# 1. Install library sistem yang dibutuhkan (Termasuk libzip-dev untuk error Anda)
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# 2. Aktifkan Apache rewrite untuk routing Laravel
RUN a2enmod rewrite

# 3. Lokasi folder aplikasi
WORKDIR /var/www/html

# 4. Copy file project
COPY . .

# 5. Pasang Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Jalankan instalasi library (Ini yang tadinya error)
RUN composer install --no-dev --optimize-autoloader

# 7. Berikan akses izin folder
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80