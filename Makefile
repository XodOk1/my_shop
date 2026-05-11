-include Makefile.local

PHP ?= php
COMPOSER ?= composer
NPM ?= npm
DOCKER_COMPOSE ?= docker compose
SERVICE ?= php
SHELL_CMD ?= sh

CONTAINER ?= php

.PHONY: help install install-backend install-frontend build dev watch test cache-clear docker-up docker-build docker-down docker-logs docker-shell

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?##' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS=":.*?## "}; {printf "%-22s %s\n", $$1, $$2}'

install: install-backend install-frontend ## Install PHP and JS dependencies

install-backend: ## Install composer dependencies
	$(COMPOSER) install --no-interaction

install-frontend: ## Install Node dependencies
	$(NPM) install

build: ## Build frontend assets for production
	$(NPM) run build

dev: ## Build frontend assets for development
	$(NPM) run dev

watch: ## Rebuild assets on file changes
	$(NPM) run watch

test: ## Run PHPUnit test suite
	$(PHP) bin/phpunit

cache-clear: ## Clear Symfony cache
	$(PHP) bin/console cache:clear

docker-up: ## Start containers in the background
	$(DOCKER_COMPOSE) up -d

docker-build: ## Build docker images
	$(DOCKER_COMPOSE) build

docker-down: ## Stop and remove containers
	$(DOCKER_COMPOSE) down

docker-logs: ## Tail docker logs
	$(DOCKER_COMPOSE) logs -f

docker-shell: ## Open a shell inside a service container (SERVICE=php, SHELL_CMD=sh)
	$(DOCKER_COMPOSE) exec $(SERVICE) $(SHELL_CMD)



bash:
	$(DOCKER_COMPOSE) exec -it ${CONTAINER} /bin/bash
