##############################
##        PHP  8.3          ##
##############################

ARG PHP_VERSION=8.3-fpm-alpine

FROM php:${PHP_VERSION}

# Bibliotecas do PHP
RUN apk --update add  zlib-dev \
    libzip-dev \
    libpng-dev \
    libpq \
    vim \
    fcgi \
    libxml2-dev \
    openssl \
    openssl-dev \
    iputils \
    libxslt-dev \
    libgcrypt-dev \
    libmcrypt-dev \
    gmp-dev \
    libpq-dev \
    libcurl \
    curl-dev \
    curl \
    acl \
    file \
    gettext \
    git \
    gnu-libiconv \
    gcompat \
    bind-tools\
    bash build-base gcc wget git autoconf libmcrypt-dev libzip-dev zip linux-headers

RUN docker-php-ext-install pdo pdo_mysql session xml bcmath opcache curl calendar

RUN docker-php-ext-install zip simplexml pcntl gd fileinfo

WORKDIR /var/www

EXPOSE 9004

#################################
##         LOGS PHP            ##
#################################

RUN mkdir -p /var/log/php
RUN chown www-data:adm /var/log/php
RUN chmod 755 /var/log/php

#################################
##           COMPOSER          ##
#################################

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#################################
##          XDEBUG             ##
#################################

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

EXPOSE 9003

#################################
##         NGINX 1.19.6        ##
#################################

RUN apk --update add nginx


#################################
##          NODE.JS           ##
#################################

RUN apk update && \
    apk add --no-cache nodejs npm

EXPOSE 3000

#################################
##         SUPERVISOR          ##
#################################

### apt-utils é um extensão de recursos do gerenciador de pacotes APT
USER root
WORKDIR /var/www
RUN apk --update add supervisor

COPY ./docker/supervisord/supervisord.conf /etc/supervisord.conf

RUN chmod -R 775 /var/www/ && chown -R www-data:www-data /var/www/

CMD  /usr/bin/supervisord -c /etc/supervisord.conf