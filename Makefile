DOCKER_COMPOSE ?= docker compose

.PHONY: up down migrate seed test queue schedule npm-dev npm-build shell

up:
	$(DOCKER_COMPOSE) up --build

down:
	$(DOCKER_COMPOSE) down

migrate:
	$(DOCKER_COMPOSE) exec app php artisan migrate

seed:
	$(DOCKER_COMPOSE) exec app php artisan db:seed

test:
	$(DOCKER_COMPOSE) exec app php artisan test

queue:
	$(DOCKER_COMPOSE) exec app php artisan queue:work

schedule:
	$(DOCKER_COMPOSE) exec app php artisan schedule:work

npm-dev:
	$(DOCKER_COMPOSE) exec app npm run dev

npm-build:
	$(DOCKER_COMPOSE) exec app npm run build

shell:
	$(DOCKER_COMPOSE) exec app bash
