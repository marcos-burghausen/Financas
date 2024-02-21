FROM composer:2.6 as build
WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist --no-scripts --no-progress

FROM php:8.2-apache-bullseye

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql

COPY --from=build /app /var/www/html

RUN a2enmod rewrite
