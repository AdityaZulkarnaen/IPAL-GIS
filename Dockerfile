FROM php:8.4-fpm

# Install modules
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions @composer gd curl xml intl mbstring zip bcmath pdo pdo_mysql mysqli apcu exif

WORKDIR /var/www/

RUN chown -R www-data:www-data /var/www/
