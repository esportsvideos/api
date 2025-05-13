PHP_CS_FIXER=docker run -it --user $$(id -u):$$(id -g) --rm -v "./:/project" -w /project jakzal/phpqa:php8.3-alpine php-cs-fixer

##
###-----------------#
###    Q&A tools    #
###-----------------#
##

cs: ## Check php code style
	$(PHP_CS_FIXER) fix --diff --dry-run

fix-cs: ## Fix php code style
	$(PHP_CS_FIXER) fix

qa: cs ## Run all Q&A tools

.PHONY: cs fix-cs qa

##
###---------------------#
###    Help & Others    #
###---------------------#
##

.DEFAULT_GOAL := help

help: ## Display help messages from parent Makefile
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-20s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

.PHONY: help
