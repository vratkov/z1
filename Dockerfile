FROM php:8.0-apache

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN apt-get update -qq \
    && apt-get upgrade -y \
    && apt-get -y -qq install \
      git \
      vim \
      nano \
      wget \
      zip \
      unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

RUN composer install --ignore-platform-reqs

COPY ./php ./
