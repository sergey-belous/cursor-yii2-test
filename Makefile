DOCKER_COMPOSE = docker compose
PHP_SERVICE = php

.PHONY: build up down logs bash composer migrate install

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
