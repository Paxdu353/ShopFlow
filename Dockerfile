FROM php:8.5-cli

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        librabbitmq-dev \
        libssl-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install \
        intl \
        pdo_pgsql \
        mbstring \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction --prefer-dist --no-progress

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "bin/dev-router.php"]
