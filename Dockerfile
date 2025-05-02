FROM php:8.1-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_mysql
