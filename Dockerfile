FROM php:7.2-apache

# install git (needed by composer later)
RUN apt-get update && apt-get install -y git

# copy composer from the composer Docker image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy the codebase into the working directory
COPY ./ /var/www/html/

# set the workdir (this is the default, but best to be explicit)
WORKDIR /var/www/html/

# run composer to install dependencies
RUN composer install
