FROM php:7.2-apache

# copy the codebase into the working directory
COPY ./ /var/www/html/
