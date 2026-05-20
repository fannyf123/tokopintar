FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    git curl unzip \
    icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev sqlite-dev postgresql-dev libxml2-dev \
    nodejs npm bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql pdo_sqlite mbstring zip gd intl opcache bcmath xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN composer dump-autoload --optimize \
    && npm run build \
    && rm -rf node_modules

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan tokopintar:ensure-schema \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan storage:link --force \
    && php artisan serve --host=0.0.0.0 --port=$PORT
