FROM php:7.1-cli

RUN export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" &&\
    apt-get update &&\
    apt-get install -y unzip libz-dev libxml2-dev libmagickwand-dev git tar &&\
    rm -r /var/lib/apt/lists/* &&\
    pecl install imagick-3.4.3 &&\
    docker-php-ext-enable imagick

RUN docker-php-ext-install zip pdo pdo_mysql intl mysqli

ARG imagemagic_config=/etc/ImageMagick-6/policy.xml

RUN if [ -f $imagemagic_config ] ; then sed -i 's/<policy domain="coder" rights="none" pattern="PDF" \/>/<policy domain="coder" rights="read|write" pattern="PDF" \/>/g' $imagemagic_config ; else echo did not see file $imagemagic_config ; fi


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data

WORKDIR /app

EXPOSE 8000
