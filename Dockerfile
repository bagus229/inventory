# syntax=docker/dockerfile:1

FROM php:8.2-cli

# Install extension PHP yang dibutuhkan CodeIgniter 4 + driver MySQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
       mysqli \
       intl \
       zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

COPY . .

RUN mkdir -p writable/cache writable/logs writable/session writable/uploads writable/debugbar \
    && chmod -R 775 writable

ENV PORT=8080
EXPOSE 8080

CMD php -d variables_order=EGPCS -S 0.0.0.0:${PORT} -t public public/router.php