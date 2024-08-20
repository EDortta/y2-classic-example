FROM php:8.2-fpm-alpine
RUN apk update
RUN apk add --no-cache pcre-dev

RUN apk add --no-cache postgresql-dev 
RUN docker-php-ext-install -j5 pdo_pgsql

RUN apk add --no-cache mysql-dev
RUN docker-php-ext-install -j5 pdo_mysql

RUN docker-php-ext-install -j5 opcache
RUN docker-php-ext-install -j5 session

RUN apk add --no-cache libzip-dev
RUN apk add --no-cache libcurl
RUN apk add --no-cache curl-dev
RUN docker-php-ext-install -j5 curl

RUN apk add --no-cache libxml2-dev
RUN apk add --no-cache libxml2
RUN apk add --no-cache libxml2-utils
RUN docker-php-ext-install -j5 xml

RUN apk add --no-cache libsodium
RUN apk add --no-cache libsodium-dev
RUN docker-php-ext-install -j5 sodium

RUN apk add --no-cache oniguruma 
RUN apk add --no-cache oniguruma-dev
RUN docker-php-ext-install -j5 mbstring

RUN apk add --no-cache $PHPIZE_DEPS
RUN pecl channel-update pecl.php.net
RUN pecl install redis-stable
RUN docker-php-ext-enable redis
RUN pecl install openswoole
RUN docker-php-ext-enable openswoole

RUN mkdir -p /var/log/php-fpm /var/log/php
RUN touch /var/log/php-fpm/www-error.log
RUN touch /var/log/php/php_errors.log
RUN chown -R www-data:www-data /var/log/php
RUN chown -R www-data:www-data /var/log/php-fpm
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY php-debug.ini /usr/local/etc/php/conf.d/php-debug.ini
EXPOSE 9000
CMD ["php-fpm"]

