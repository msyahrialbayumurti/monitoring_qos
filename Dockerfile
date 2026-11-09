FROM php:8.2-cli

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan schedule:work & exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
