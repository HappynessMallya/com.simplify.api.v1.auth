ARG VERSION

# Dev image
FROM php:${VERSION}-fpm-alpine AS prod

## Install system dependencies
RUN apk update && \
    apk add --no-cache --virtual dev-deps git autoconf gcc g++ make && \
    apk add --no-cache zlib-dev libzip-dev

## Install php extensions
RUN docker-php-ext-install pdo_mysql zip

## Copy php default configuration
COPY ./docker/php-fpm/default.ini /usr/local/etc/php/conf.d/default.ini

ENV APP_ENV=prod
WORKDIR /app

## Install composer
RUN wget https://getcomposer.org/installer && \
    php installer --version=1.8.6 --install-dir=/usr/local/bin/ --filename=composer && \
    rm installer && \
    composer global require hirak/prestissimo

## Copy project files to workdir
COPY . .

## Remove dev dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

## supervisor
RUN apk --update add supervisor
ADD ./docker/php-fpm/supervisord.conf /etc/supervisord.conf

## Cleanup
RUN apk del dev-deps && \
    composer global remove hirak/prestissimo && \
    rm /usr/local/bin/composer