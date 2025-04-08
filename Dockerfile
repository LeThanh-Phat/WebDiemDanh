# PHP base image có sẵn composer và các extension cần thiết
FROM php:8.2-cli

# Cài các tiện ích và PHP extension cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Cài Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Tạo thư mục project và sao chép file
WORKDIR /var/www

COPY . .

# Cài thư viện Laravel (tăng log chi tiết nếu lỗi)
RUN composer install --no-dev --optimize-autoloader -vvv

# Set quyền nếu Laravel cần
RUN chmod -R 775 storage bootstrap/cache

# Expose cổng và khởi động Laravel
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
