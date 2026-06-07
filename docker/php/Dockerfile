FROM node:22.21.1-bookworm-slim AS node

FROM composer:2.8 AS composer

FROM php:8.4-fpm-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        git \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        bcmath \
        intl \
        opcache \
        pdo_pgsql \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local /usr/local

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/mural-lens-entrypoint
RUN chmod +x /usr/local/bin/mural-lens-entrypoint

ENTRYPOINT ["mural-lens-entrypoint"]
CMD ["php-fpm"]
