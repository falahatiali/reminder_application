FROM php:8.0-fpm-alpine

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql

COPY crond /etc/crontabs/root

CMD ["crond" , "-f"]
