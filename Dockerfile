FROM php:8.2-apache

# 1. Install library sistem & PHP extension (Termasuk ZIP dan GD)
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# 2. FIX: Memastikan hanya mpm_prefork yang aktif agar tidak error "More than one MPM loaded"
RUN a2dismod mpm_event || true && a2enmod mpm_prefork || true

# 3. Aktifkan Apache rewrite untuk Laravel
RUN a2enmod rewrite

# 4. Set lokasi aplikasi
WORKDIR /var/www/html
COPY . .

# 5. Pasang Composer dan install library Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 6. Atur izin akses folder
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80