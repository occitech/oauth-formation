SHELL=/usr/bin/env bash

start: docker-compose.yml
	@docker-compose up -d web db pma

install:
	@docker-compose run --rm composer install --dev
	@docker-compose run --rm symfony assets:install public
	@docker-compose run --rm symfony cache:clear

migrate:
	@docker-compose run --rm symfony doctrine:database:create --env dev --if-not-exists
	@docker-compose run --rm symfony doctrine:migrations:migrate