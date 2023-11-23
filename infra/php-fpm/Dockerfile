FROM php:8.2-fpm-alpine
RUN apk update && apk add --no-cache postgresql-dev
RUN docker-php-ext-install pdo_pgsql
RUN mkdir /var/log/php-fpm /var/log/php
RUN touch /var/log/php-fpm/www-error.log
RUN touch /var/log/php/php_errors.log
RUN chown -R www-data:www-data /var/log/php
RUN chown -R www-data:www-data /var/log/php-fpm
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY php-debug.ini /usr/local/etc/php/conf.d/php-debug.ini
EXPOSE 9000
CMD ["php-fpm"]

