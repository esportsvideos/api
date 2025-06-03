DOCKER=docker
DOCKER_COMPOSE=$(DOCKER) compose
DOCKER_COMPOSE_ALL=$(DOCKER) compose --profile="*"
DOCKER_PHP_CONTAINER=php
DOCKER_BUILD=$(DOCKER_COMPOSE) --file compose.build.yaml
EXEC_PHP=$(DOCKER_COMPOSE) exec --user=www-data $(DOCKER_PHP_CONTAINER)
RUN_PHP=$(DOCKER_COMPOSE) run --rm $(DOCKER_PHP_CONTAINER)
RUN_PHP_ALONE=$(DOCKER_COMPOSE) run --no-deps --rm $(DOCKER_PHP_CONTAINER)
COMPOSER=$(EXEC_PHP) composer
CONSOLE=$(EXEC_PHP) bin/console

##
###--------------#
###    Docker    #
###--------------#
##

up: ## Create and start containers
	$(DOCKER_COMPOSE) up -d
	@echo "\n##################"
	@echo "#### Services ####"
	@echo "##################\n"
	@echo "- Traefik : http://traefik.esv.localhost"
	@echo "- Api : http://api.esv.localhost"

up-all: ## Create and start containers from all profiles
	$(DOCKER_COMPOSE_ALL) up -d
	@echo "\n##################"
	@echo "#### Services ####"
	@echo "##################\n"
	@echo "- Traefik : http://traefik.esv.localhost"
	@echo "- Api : http://api.esv.localhost"
	@echo "- Adminer : http://adminer.esv.localhost"

stop: ## Stop all containers
	$(DOCKER_COMPOSE_ALL) stop

pull: ## Pull service images
	$(DOCKER_COMPOSE_ALL) pull

down: ## Stops containers and removes containers, networks, volumes, and images created by `up`.
	$(DOCKER_COMPOSE_ALL) down --volumes

sh: ## Connect to php container
	$(EXEC_PHP) sh

build: ## Build all images
	$(DOCKER_BUILD) build

bash: sh ## Alias for sh
start: up ## Alias for up
start-all: up-all ## Alias for up-all

install: pull compose.override.yaml up vendor db-migrate generate-keypair db-fixtures ## Install the project

.PHONY: up stop down pull sh build bash start start-all install

##
###----------------#
###    Doctrine    #
###----------------#
##

db-create: ## Creates the configured database.
	$(CONSOLE) doctrine:database:create --if-not-exists

db-drop: ## Drops the configured database
	$(CONSOLE) doctrine:database:drop --force --if-exists

db-migrate: ## Execute a migration to the latest available version.
	$(CONSOLE) doctrine:migrations:migrate --allow-no-migration --no-interaction --all-or-nothing

db-validate: ## Validate the doctrine ORM mapping
	$(CONSOLE) doctrine:schema:validate

db-schema: ## Dumps the SQL needed to update the database schema to match the current mapping metadata.
	$(CONSOLE) doctrine:schema:update --dump-sql

db-schema-force: ## Executes the SQL needed to update the database schema to match the current mapping metadata.
	$(CONSOLE) doctrine:schema:update --force

db-diff: ## Creates a new migration based on database changes
	$(CONSOLE) make:migration

db-update: db-diff db-migrate ## Execute db-diff & db-migrate

db-fixtures: ## Load data fixtures to your database
	$(CONSOLE) doctrine:fixtures:load --no-interaction --no-debug

db-fixtures-%: ## Load data fixtures to your database with a custom size
	$(DOCKER_COMPOSE) exec --user=www-data --env FIXTURES_SIZE=$* $(DOCKER_PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction --no-debug

fixtures: db-fixtures ## Alias for db-fixtures

LAST_MIGRATION := $(shell ls -t migrations/ | head -n 1 | sed 's/\.php$$//' | sed 's/^/DoctrineMigrations\\\\/')

db-execute-up: ## Execute the latest migration versions up manually.
	$(CONSOLE) doctrine:migrations:execute --up --no-interaction "${LAST_MIGRATION}"

db-execute-down: ## Execute the latest migration versions down manually.
	$(CONSOLE) doctrine:migrations:execute --down --no-interaction "${LAST_MIGRATION}"

.PHONY: db-create db-drop db-migrate db-validate db-schema db-schema-force db-diff db-update 
.PHONY: db-fixtures fixtures db-execute-up db-execute-down

##
###-----------------#
###    Q&A tools    #
###-----------------#
##

lint-config: ## Lint yaml for config directory
	$(CONSOLE) lint:yaml config

lint-container: ## Ensures that arguments injected into services match type declarations
	$(CONSOLE) lint:container

lint: lint-config lint-container ## Lint twig and yaml files

PHP_CS_FIXER=docker run --user $$(id -u):$$(id -g) --rm -v "./:/code" -w /code ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.3

cs: ## Check php code style
	$(PHP_CS_FIXER) fix --diff --dry-run

fix-cs: ## Fix php code style
	$(PHP_CS_FIXER) fix

phpstan: ## Analyze php code
	$(RUN_PHP_ALONE) ./vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G

stan: phpstan ## Alias for phpstan
qa: cs phpstan composer-validate ## Run all Q&A tools

.PHONY: cs fix-cs phpstan qa stan

##
###----------------#
###    Composer    #
###----------------#
##

composer-validate: ## Validates a composer.json and composer.lock.
	$(COMPOSER) validate

composer-update: ## Upgrades your dependencies to the latest version according to composer.json, and updates the composer.lock file
	$(COMPOSER) update $(package)

composer-require: ## Adds required packages to your composer.json and installs them (Ex: package=orm composer-require)
	$(COMPOSER) require $(package)

.PHONY: composer-validate composer-update composer-require

##
###----------------------------#
###    Rules based on files    #
###----------------------------#
##

vendor:	composer.lock ## Install dependencies
	$(COMPOSER) install

compose.override.yaml: compose.override.yaml.dist ## Create compose.override.yaml
	cp compose.override.yaml.dist compose.override.yaml

##
###-------------#
###    Utils    #
###-------------#
##

cc:	## Clear cache
	$(CONSOLE) cache:clear

cc-rm: ## Clear cache by rm -rf
	rm -rf var/cache

config/jwt/public.pem: ## Generate public/private keys for jwt.
	$(CONSOLE) lexik:jwt:generate-keypair

generate-keypair: config/jwt/public.pem ## Generate public/private keys for jwt.

.PHONY: cc cc-rm generate-keypair

##
###---------------------#
###    Help & Others    #
###---------------------#
##

.DEFAULT_GOAL := help

help: ## Display help messages from parent Makefile
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.PHONY: help
