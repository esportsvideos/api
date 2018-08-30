# Vars
VAR_PHP_CONTAINER=docker-compose exec php
VAR_MYSQL_CONTAINER=docker-compose exec mysql
VAR_COMPOSER=$(VAR_PHP_CONTAINER) composer
VAR_CONSOLE=$(VAR_PHP_CONTAINER) php bin/console
UNAME_S=$(shell uname -s)

# If Linux, we share the uid of current user to set it to www-data in docker
ifeq (Linux,$(UNAME_S))
VAR_BUILD_ARG=--build-arg AS_UID=$(shell id -u)
endif

.DEFAULT_GOAL := help

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
install: build up jwt undist composer-install schema-create fixtures

# Build docker images
.PHONY: build
build:
	docker-compose build $(VAR_BUILD_ARG) php
	docker-compose build mysql nginx

# Create the public & private key for JWT
.PHONY: jwt
jwt:
	$(VAR_PHP_CONTAINER) mkdir -p config/jwt
	$(VAR_PHP_CONTAINER) openssl genrsa -out config/jwt/private.pem 4096
	$(VAR_PHP_CONTAINER) openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Launch the docker dev stack
.PHONY: up
up:
	docker-compose up -d

# Stop the docker dev stack
.PHONY: stop
stop:
	docker-compose stop

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

# The validate command validates a given composer.json and composer.lock
.PHONY: composer-validate
composer-validate:
	$(VAR_COMPOSER) validate --strict

# Symfony's Console
.PHONY: console
console:
	$(VAR_CONSOLE) $(ARG)

# Launch php cs fixer
.PHONY: php-cs
php-cs:
	$(VAR_PHP_CONTAINER) php vendor/bin/php-cs-fixer fix ./src

# Launch php cs fixer with dry run
.PHONY: php-cs-dry
php-cs-dry:
	$(VAR_PHP_CONTAINER) php vendor/bin/php-cs-fixer fix ./src --diff --dry-run

# Checks security issues in your project dependencies
.PHONY: security-check
security-check:
	$(VAR_CONSOLE) security:check

# Lints a file and outputs encountered errors
.PHONY: lint-yaml
lint-yaml:
	$(VAR_CONSOLE) lint:yaml config

# Lints a template and outputs encountered errors
.PHONY: lint-twig
lint-twig:
	$(VAR_CONSOLE) lint:twig templates

# Execute travis tests
.PHONY: travis
travis: php-cs-dry lint-twig lint-yaml security-check composer-validate schema-validate behat

# Load data fixtures to your database.
.PHONY: fixtures
fixtures:
	$(VAR_CONSOLE) doctrine:fixtures:load --no-interaction

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

# Executes (or dumps) the SQL needed to generate the database schema
.PHONY: schema-create
schema-create:
	$(VAR_CONSOLE) doctrine:schema:create --no-interaction

# Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata.
.PHONY: schema-update
schema-update:
	$(VAR_CONSOLE) doctrine:schema:update --force

# Validate the mapping files.
.PHONY: schema-validate
schema-validate:
	$(VAR_CONSOLE) doctrine:schema:validate --skip-sync --no-interaction

# Executes behat test
.PHONY: behat
behat:
	$(VAR_PHP_CONTAINER) php bin/behat $(ARG)
