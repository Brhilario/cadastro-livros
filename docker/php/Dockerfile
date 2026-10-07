FROM php:8.2-fpm-alpine

# Instala dependências do sistema e extensões PDO necessárias
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]