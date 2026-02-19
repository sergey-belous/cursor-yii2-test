DOCKER_COMPOSE = docker compose
PHP_SERVICE = php
NODE_SERVICE = node

.PHONY: build up down logs bash composer migrate install npm-install npm-dev npm-build

build:
	$(DOCKER_COMPOSE) build

up:
	$(DOCKER_COMPOSE) up -d

down:
	$(DOCKER_COMPOSE) down

logs:
	$(DOCKER_COMPOSE) logs -f --tail=200

bash:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) bash

composer:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer $(ARGS)

install:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer install

migrate:
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php yii migrate --interactive=0

npm-install:
	$(DOCKER_COMPOSE) run --rm $(NODE_SERVICE) npm install

npm-dev:
	$(DOCKER_COMPOSE) run --rm --service-ports $(NODE_SERVICE) npm run dev -- --host 0.0.0.0

npm-build:
	$(DOCKER_COMPOSE) run --rm $(NODE_SERVICE) npm run build
