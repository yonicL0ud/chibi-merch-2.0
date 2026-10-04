# Imagen de Dev Containers con PHP 8.3 y Composer
FROM mcr.microsoft.com/devcontainers/php:8.3

# Driver de MySQL para PHP + cliente mysql de consola
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
 default-mysql-client libzip-dev unzip \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*
