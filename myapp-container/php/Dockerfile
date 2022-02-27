FROM php:7.4-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    @composer-2.2.7 \
    bcmath \
    calendar \
    exif \
    gd \
    gettext \
    http \
    imagick \
    mcrypt \
    mysqli \
    opcache \
    pdo_mysql \
    uuid \
    xdebug-3.1.3 \
    zip

RUN apt-get update && \
    apt-get install -y nodejs npm

COPY "./conf/php/php.ini" "/usr/local/etc/php/php.ini"
COPY "./conf/www/www.conf" "/usr/local/etc/php-fpm.d/www.conf"

COPY "./entrypoint.sh" "/usr/local/bin/ext-entrypoint.sh"
RUN chmod +x "/usr/local/bin/ext-entrypoint.sh"
ENTRYPOINT "/usr/local/bin/ext-entrypoint.sh"
