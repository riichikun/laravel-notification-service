FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    librabbitmq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql pgsql gd zip sockets bcmath \
    && pecl install amqp redis \
    && docker-php-ext-enable amqp redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
