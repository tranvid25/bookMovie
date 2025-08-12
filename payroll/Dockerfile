# Sử dụng PHP 8.2 FPM image chính thức
FROM php:8.2-fpm

# Cài các thư viện cần thiết
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql zip bcmath mbstring xml tokenizer

# Cài Composer (copy từ image composer chính thức)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục làm việc trong container
WORKDIR /var/www

# Copy toàn bộ code vào container
COPY . .

# Chạy composer install, không tải dev packages
RUN composer install --no-dev --optimize-autoloader

# Cache config, route, view
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Đổi quyền thư mục storage và bootstrap cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Lắng nghe port 9000 của php-fpm
EXPOSE 9000

# Lệnh chạy PHP-FPM khi container khởi động
CMD ["php-fpm"]
