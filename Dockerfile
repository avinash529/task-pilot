FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./vite.config.js
RUN npm run build

FROM php:8.2-fpm-alpine
RUN apk add --no-cache nginx mysql-client libzip-dev oniguruma-dev icu-dev \
    && docker-php-ext-install pdo_mysql intl
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY .docker/nginx/default.conf /etc/nginx/http.d/default.conf
RUN chown -R www-data:www-data storage bootstrap/cache
CMD ["php-fpm", "-F"]
