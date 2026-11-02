FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        libonig-dev \
    && docker-php-ext-install \
        intl \
        pdo_pgsql \
        mbstring \
        zip \
        opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]