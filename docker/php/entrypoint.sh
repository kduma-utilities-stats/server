#!/bin/sh

php /var/www/html/artisan migrate --force && php-fpm
