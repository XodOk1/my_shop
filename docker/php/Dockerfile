FROM php:8.3-fpm

ARG APP_UID=1000
ARG APP_GID=1000

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_mysql \
    gd \
    zip \
    opcache \
    bcmath \
  && pecl install apcu \
  && docker-php-ext-enable apcu \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN usermod -u ${APP_UID} www-data && groupmod -g ${APP_GID} www-data

WORKDIR /var/www
