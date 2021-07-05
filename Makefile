SHELL := /bin/bash
## Default development
env = 'prod'

create-db: ## Create db
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console doctrine:database:create

delete-db: ## Delete db
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console doctrine:database:drop

migration: ## Execute migration
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console doctrine:schema:update -force -no-interaction

up: ## Run local docker containers
	-@docker-compose --file docker-compose.$(env).yaml up -d

build: ## Build local docker images
	-@docker-compose --file docker-compose.$(env).yaml build --no-cache

down: ## Stop and remove containers, networks, images, and volumes
	-@docker-compose --file docker-compose.$(env).yaml down -v --remove-orphans
	-@echo sudo rm -rf var/cache && sudo chmod -R 777 var

ps: ## list local docker containers
	-@docker-compose --file docker-compose.$(env).yaml ps

stop: ## Stop local docker containers
	-@docker-compose --file docker-compose.$(env).yaml stop

kill: ## Kill local docker containers
	-@docker-compose --file docker-compose.$(env).yaml kill

restart: kill up ## Restart local docker containers

help:
	@echo 'Usage: make [target] ...'
	@echo
	@echo 'targets:'
	@echo -e "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)"

.DEFAULT_GOAL := help
