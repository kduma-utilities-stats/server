FROM php:8.1-cli-alpine AS composer_base
WORKDIR /var/www/html

COPY --from=composer /usr/bin/composer /usr/bin/composer



FROM node:19-alpine AS vite_base
WORKDIR /var/www/html



FROM php:8.1-fpm-alpine AS php_base
WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install opcache

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini






FROM nginx:alpine AS nginx
WORKDIR /var/www/html


COPY docker/nginx/default.conf /etc/nginx/conf.d


COPY build/nginx/ /var/www/html/



FROM php_base AS fpm
WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /var/www/entrypoint.sh
RUN chmod +x /var/www/entrypoint.sh

COPY build/php/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false

VOLUME /var/www/html/storage/app/jobs

CMD ["/var/www/entrypoint.sh"]



FROM php_base AS cron
WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /var/www/entrypoint.sh
RUN chmod +x /var/www/entrypoint.sh

COPY build/php/ /var/www/html/

COPY docker/php/crontab /etc/crontabs/root

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false

VOLUME /var/www/html/storage/app/jobs

CMD ["crond", "-f"]
