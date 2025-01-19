# Gunakan PHP 8.3 dengan FPM
FROM php:8.3-fpm

# Install dependensi sistem
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif bcmath gd mysqli

# Install Composer versi tertentu
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=2.8.4

# Set direktori kerja
WORKDIR /var/www/html

# Tambahkan file `artisan` jika tidak ada
ADD https://raw.githubusercontent.com/laravel/laravel/10.x/artisan artisan

# Salin seluruh aplikasi Laravel terlebih dahulu
COPY . .

# Jalankan Composer untuk menginstal dependensi
RUN composer install --no-dev --optimize-autoloader

# Buat folder bootstrap/cache jika tidak ada
RUN mkdir -p bootstrap/cache && chmod -R 775 bootstrap/cache

# Atur izin untuk storage dan cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
