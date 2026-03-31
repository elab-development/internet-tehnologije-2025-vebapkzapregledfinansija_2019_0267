FROM php:8.2-fpm

# Instalacija ekstenzija
RUN docker-php-ext-install pdo pdo_mysql

# Radni direktorijum
WORKDIR /var/www

# Kopiraj composer i instaliraj
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

RUN composer install

CMD ["php-fpm"]