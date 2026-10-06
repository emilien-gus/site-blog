FROM php:8.2-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем расширения PHP для работы с MySQL
RUN docker-php-ext-install pdo pdo_mysql zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Указываем рабочую директорию
WORKDIR /var/www/html

# Копируем конфигурацию composer
COPY composer.json composer.lock* ./

# Устанавливаем зависимости
# --prefer-dist ускоряет установку, --no-interaction убирает интерактивные вопросы
RUN composer install --prefer-dist --no-scripts --no-interaction

# Копируем исходный код проекта
COPY . .

# Компилируем SCSS в CSS на этапе сборки образа
RUN php bin/compile-css.php || true

# Копируем и настраиваем стартовый entrypoint-скрипт
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
