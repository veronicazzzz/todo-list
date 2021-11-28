FROM php:7.4-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
libzip-dev

RUN docker-php-ext-install zip
RUN pecl install xdebug raphf propro
RUN docker-php-ext-enable apcu xdebug raphf propro

RUN echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && composer --version

CMD php-fpm