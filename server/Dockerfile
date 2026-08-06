FROM node:26.5.0-alpine AS FRONTEND-BUILDER

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

RUN /bin/sh -c "npm run build"

RUN rm -rf node_modules \
    && rm -f package.json package-lock.json


FROM composer:2.10 AS BACKEND-BUILDER

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress

COPY --from=FRONTEND-BUILDER /app .

RUN composer dump-autoload \
    --optimize \
    --classmap-authoritative


FROM php:8.3-apache

RUN apk add --no-cache \
        $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql \
    && apk del $PHPIZE_DEPS

WORKDIR /var/www/html

COPY --from=BACKEND-builder /app .

RUN mkdir -p storage/logs \
    && touch storage/logs/app.log \
    && chown -R www-data:www-data storage \
    && chmod -R 775 storage

RUN sed -i 's/^listen = .*/listen = 0.0.0.0:9000/' \
        /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^listen.allowed_clients/;listen.allowed_clients/' \
        /usr/local/etc/php-fpm.d/www.conf

EXPOSE 9000