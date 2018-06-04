# Vars
VAR_PHP_CONTAINER=docker-compose exec php
VAR_COMPOSER=$(VAR_PHP_CONTAINER) composer
VAR_CONSOLE=$(VAR_PHP_CONTAINER) php bin/console
UNAME_S=$(shell uname -s)

# If Linux, we share the uid of current user to set it to www-data in docker
ifeq (Linux,$(UNAME_S))
VAR_BUILD_ARG=--build-arg AS_UID=$(shell id -u)
endif

# Help
.PHONY: help
help:
	@ echo
	@ echo '  Usage:'
	@ echo ''
	@ echo "    make <target> [ARG='']"
	@ echo ''
	@ echo '  Targets:'
	@ echo ''
	@ awk '/^#/{ comment = substr($$0,3) } comment && /^[a-zA-Z][a-zA-Z0-9_-]+ ?:/{ print "   ", $$1, comment }' $(MAKEFILE_LIST) | column -t -s ':' | sort
	@ echo ''

# Install the project
.PHONY: install
install: build up jwt undist composer-install fixtures

# Build docker images
.PHONY: build
build:
	docker-compose build $(VAR_BUILD_ARG) php
	docker-compose build mysql nginx

# Create the public & private key for JWT
.PHONY: jwt
jwt:
	$(VAR_PHP_CONTAINER) mkdir var/jwt
	$(VAR_PHP_CONTAINER) openssl genrsa -out var/jwt/private.pem 4096
	$(VAR_PHP_CONTAINER) openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem

# Launch the docker dev stack
.PHONY: up
up:
ifeq (Darwin,$(UNAME_S))
	-docker-sync start
	docker-compose -f docker-compose.yml -f docker-compose.sync.yml up -d
else
	docker-compose up -d
endif

# Stop the docker dev stack
.PHONY: stop
stop:
ifeq (Darwin,$(UNAME_S))
	docker-compose -f docker-compose.yml -f docker-compose.sync.yml stop
else
	docker-compose stop
endif

# Connect to the php container
.PHONY: bash
bash:
	docker-compose exec php /bin/sh

# Execute composer require
.PHONY: composer-require
composer-require:
	test $(ARG)
	$(VAR_COMPOSER) require $(ARG)

# Execute composer install
.PHONY: composer-install
composer-install:
	$(VAR_COMPOSER) install -o

# Execute composer update
.PHONY: composer-update
composer-update:
	$(VAR_COMPOSER) update $(ARG)

# Symfony's Console
.PHONY: console
console:
	$(VAR_CONSOLE) $(ARG)

# Launch php cs fixer
.PHONY: php-cs
php-cs:
	$(VAR_PHP_CONTAINER) php vendor/bin/php-cs-fixer fix ./src

# Execute travis tests
.PHONY: travis
travis:
	$(VAR_PHP_CONTAINER) php bin/php-cs-fixer fix ./src --diff --dry-run -v
	$(VAR_CONSOLE) lint:yaml config
	$(VAR_CONSOLE) lint:twig templates
	$(VAR_CONSOLE) security:check --end-point=http://security.sensiolabs.org/check_lock
	$(VAR_COMPOSER) validate --strict
	$(VAR_CONSOLE) doctrine:schema:validate --skip-sync -vvv --no-interaction
	$(VAR_CONSOLE) bin/behat -f progress

# Load data fixtures to your database.
.PHONY: fixtures
fixtures:
	$(VAR_CONSOLE) doctrine:fixtures:load

# View the status of a set of migrations.
.PHONY: migration-status
migration-status:
	$(VAR_CONSOLE) doctrine:migration:status

# Execute a migration to a specified version or the latest available version.
.PHONY: migration-migrate
migration-migrate:
	$(VAR_CONSOLE) doctrine:migration:migrate $(ARG)

# Execute a single migration version up or down manually.
.PHONY: migration-execute
migration-execute:
	test $(ARG)
	$(VAR_CONSOLE) doctrine:migrations:execute $(ARG)

# Generate a migration by comparing your current database to your mapping information.
.PHONY: migration-diff
migration-diff:
	$(VAR_CONSOLE) doctrine:migrations:diff

# Copy all .dist files to base files.
.PHONY: undist
undist:
	cp behat.yml.dist behat.yml
	cp .env.dist .env
	cp .php_cs.dist .php_cs
	cp phpunit.xml.dist phpunit.xml
