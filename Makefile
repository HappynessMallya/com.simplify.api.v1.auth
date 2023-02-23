SHELL := /bin/bash
## Default development
env = 'dev'

deploy: ## Execute command for deploy in production mode
	-$(SHELL) deploy.sh

service-bus: ## Consuming Messages (Running the Worker)
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console messenger:consume async -vv

lint: ## Run the php linter over the code
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php ./vendor/bin/phpcs

cache-clear: ## Clear service cache
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console cache:clear

create-db: ## Create db
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console doctrine:database:create --if-not-exists

delete-db: ## Delete db
	-@docker-compose --file docker-compose.dev.yaml exec php-fpm php bin/console doctrine:database:drop --force

migration: ## Execute migration
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php bin/console doctrine:migration:migrate --no-interaction

up: ## Run local docker containers
	-@docker-compose --file docker-compose.$(env).yaml up -d

build: ## Build local docker images
	-@docker-compose --file docker-compose.$(env).yaml build --no-cache

unit: ## Execute unit tests
	-@docker-compose --file docker-compose.$(env).yaml exec php-fpm php ./vendor/bin/simple-phpunit tests/Unit/

integration: ## Execute integration tests
	-@echo 'Run integration test...'
	-@docker-compose --file docker-compose.dev.yaml exec php-fpm sh -c 'APP_ENV=test php ./vendor/bin/simple-phpunit tests/Integration/'
	-@echo 'Integration test done.'

down: ## Stop and remove containers, networks, images, and volumes
	-@docker-compose --file docker-compose.$(env).yaml down -v --rmi local --remove-orphans
	-@sudo rm -rf var/cache && sudo chmod -R 777 var

ps: ## list local docker containers
	-@docker-compose --file docker-compose.$(env).yaml ps

kill: ## Kill local docker containers
	-@docker-compose --file docker-compose.$(env).yaml kill

restart: kill up ## Restart local docker containers

help:
	@echo 'Usage: make [target] ...'
	@echo
	@echo 'targets:'
	@echo -e "$$(grep -hE '^\S+:.*##' $(MAKEFILE_LIST) | sed -e 's/:.*##\s*/:/' -e 's/^\(.\+\):\(.*\)/\\x1b[36m\1\\x1b[m:\2/' | column -c2 -t -s :)"

.DEFAULT_GOAL := help
