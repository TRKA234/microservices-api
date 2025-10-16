FROM php:8.2-apache
COPY src/ /var/www/html/
#enable useful Apache modules
RUN a2enmod rewrite
RUN docker-php-ext-install pdo_mysql mysqli && docker-php-ext-enable pdo_mysql mysqli

WORKDIR /var/www/html