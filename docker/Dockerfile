FROM ghcr.io/esportsvideos/php:1.0.0 AS php

# ENTRYPOINT
USER root
COPY docker/php/docker-entrypoint.prod.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# APPLICATION
USER www-data
WORKDIR /var/www
COPY --chown=www-data:www-data composer.* symfony.lock .env ./

RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts --no-dev --no-progress --no-suggest; \
	composer clear-cache;

COPY --chown=www-data:www-data bin bin/
COPY --chown=www-data:www-data config config/
COPY --chown=www-data:www-data public public/
#COPY --chown=www-data:www-data migrations migrations/
COPY --chown=www-data:www-data src src/
#COPY --chown=www-data:www-data templates templates/

RUN set -eux; \
    mkdir -p var/cache var/log; \
	composer dump-autoload --no-dev --classmap-authoritative; \
    composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
    rm .env

# CONFIGS
COPY docker/php/conf.d/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini

VOLUME /var/www/var

FROM nginx:1.27.5-alpine AS nginx

COPY docker/nginx/templates /etc/nginx/templates

# https://github.com/moby/moby/issues/37965#issuecomment-426853382
RUN true
COPY --from=php /var/www/public /var/www/public
