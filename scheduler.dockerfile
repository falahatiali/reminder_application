FROM php:8.0.2-fpm

RUN apt-get update && apt-get install -qqy git unzip libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        cron \
        supervisor \
        vim \
        procps \
        libaio1 wget && apt-get clean autoclean && apt-get autoremove --yes &&  rm -rf /var/lib/{apt,dpkg,cache,log}/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && \
    apt-get install -y libxml2-dev

RUN  docker-php-ext-install \
               soap \
       && docker-php-ext-install \
              pdo pdo_mysql


COPY docker/scheduler/supervisod.conf /etc/supervisor/conf.d/laravel.conf
ENTRYPOINT ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
