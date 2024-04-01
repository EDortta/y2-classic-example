FROM php:8.2-fpm-alpine
RUN apk update && apk add --no-cache postgresql-dev
RUN docker-php-ext-install pdo_pgsql
RUN apk add --no-cache pcre-dev
RUN apk add --no-cache postgresql-dev 
RUN docker-php-ext-install -j5 opcache
RUN apk add --no-cache oniguruma
RUN docker-php-ext-install -j5 session
RUN docker-php-ext-install -j5 curl
RUN docker-php-ext-install -j5 xml
RUN docker-php-ext-install -j5 xmlreader
RUN docker-php-ext-install -j5 xmlwriter
RUN docker-php-ext-install -j5 sodium
RUN docker-php-ext-install -j5 mbstring

RUN mkdir /var/log/php-fpm /var/log/php
RUN touch /var/log/php-fpm/www-error.log
RUN touch /var/log/php/php_errors.log
RUN chown -R www-data:www-data /var/log/php
RUN chown -R www-data:www-data /var/log/php-fpm
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY php-debug.ini /usr/local/etc/php/conf.d/php-debug.ini
EXPOSE 9000
CMD ["php-fpm"]

