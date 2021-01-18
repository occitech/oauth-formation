FROM php:7.4-apache
MAINTAINER Occitech <contact@occitech.fr>

RUN apt-get update

RUN apt-get install -y git
RUN apt-get install -y zip
RUN apt-get install -y libicu-dev
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y libzip-dev
RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libpng-dev
RUN apt-get install -y libsodium-dev

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install intl
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install sodium

RUN rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=1.10.19

RUN sed -i "s#/var/www/html#/var/www/public#g" /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

COPY php.ini /usr/local/etc/php/conf.d