#!/bin/sh
set -e

bin/console doctrine:migrations:migrate --no-interaction

exec docker-php-entrypoint "$@"
