FROM php:7.1-cli

RUN export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" &&\
    apt-get update &&\
    apt-get install -y unzip libz-dev libxml2-dev libmagickwand-dev git tar &&\
    rm -r /var/lib/apt/lists/* &&\
    pecl install imagick-3.4.3 &&\
    docker-php-ext-enable imagick

RUN docker-php-ext-install zip pdo pdo_mysql intl mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer&& \
         composer global require hirak/prestissimo

RUN usermod -u 1000 www-data

WORKDIR /app

EXPOSE 8000
