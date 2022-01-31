# Dev image
FROM php:7.4-fpm-alpine AS dev

## Install system dependencies
RUN apk update && \
    apk add --no-cache --virtual dev-deps git autoconf gcc g++ make && \
    apk add --no-cache zlib-dev libzip-dev

## Install php extensions
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    docker-php-ext-install pdo_mysql zip

## Copy php default configuration
COPY ./docker/default.ini /usr/local/etc/php/conf.d/default.ini

ENV APP_ENV=dev
WORKDIR /app

## Install composer
RUN wget https://getcomposer.org/installer && \
    php installer --version=1.8.6 --install-dir=/usr/local/bin/ --filename=composer && \
    rm installer && \
    composer global require hirak/prestissimo

# Test image
FROM dev AS test

WORKDIR /app

## Copy project files to workdir
COPY . .

## Install application dependencies
RUN composer install --no-interaction --optimize-autoloader

## Change files owner to php-fpm default user
RUN chown -R www-data:www-data .

# Prod image
FROM test AS prod

ENV APP_ENV=prod
WORKDIR /app

## Nginx
## Install system dependencies
RUN apk update && \
    apk add --no-cache nginx

COPY ./docker/default.conf /etc/nginx/http.d/default.conf

## Remove dev dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

## supervisor
RUN apk --update add supervisor
RUN mkdir -p /var/log/supervisor
ADD ./docker/supervisor.ini /etc/supervisor.d/nginx-supervisor.ini

## Disable xdebug on production
RUN rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

## Cleanup
RUN apk del dev-deps && \
    composer global remove hirak/prestissimo && \
    rm /usr/local/bin/composer \
