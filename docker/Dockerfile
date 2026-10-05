FROM php:8.2-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP для работы с MySQL
RUN docker-php-ext-install pdo pdo_mysql zip

# Устанавливаем Composer (берем из официального образа)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Указываем рабочую директорию
WORKDIR /var/www/html