FROM php:8.4-fpm-alpine

# Installation des extensions nécessaires à Symfony
RUN apk add --no-cache \
    icu-dev \
    libpq-dev \
    bash \
    git

RUN docker-php-ext-install \
    intl \
    opcache \
    pdo_mysql

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html