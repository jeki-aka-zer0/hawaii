FROM php:8.2-cli

RUN apt-get update && apt-get install -y libpq-dev unzip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./php-cli/default.ini /usr/local/etc/php/conf.d/default.ini
COPY ./php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /api