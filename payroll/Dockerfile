# Sử dụng PHP 8.2 FPM image chính thức
FROM php:8.2-fpm

# Cài các thư viện cần thiết
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev zip libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql zip bcmath mbstring xml sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Cài Composer (copy từ image composer chính thức)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục làm việc trong container
WORKDIR /var/www

# Copy composer.* trước để tận dụng cache
COPY composer.json composer.lock ./

# Cài đặt dependencies (không tải dev packages)
RUN composer install --no-dev --optimize-autoloader

# Copy toàn bộ code vào container
COPY . .

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose cổng PHP-FPM
EXPOSE 9000

# Lệnh mặc định
CMD ["php-fpm"]
