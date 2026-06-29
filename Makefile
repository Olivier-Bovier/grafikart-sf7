.DEFAULT_GOAL := help
PHP = docker compose exec php
CONSOLE = $(PHP) php bin/console

.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-25s\033[0m %s\n", $$1, $$2}'

## —— Docker ————————————————————————————————————————————————————————————————
.PHONY: up
up: ## Démarre les conteneurs en arrière-plan
	docker compose up -d

.PHONY: down
down: ## Arrête et supprime les conteneurs
	docker compose down

.PHONY: restart
restart: down up ## Redémarre les conteneurs

.PHONY: build
build: ## Reconstruit les images Docker
	docker compose build --no-cache

.PHONY: logs
logs: ## Affiche les logs en temps réel
	docker compose logs -f

.PHONY: ps
ps: ## Liste les conteneurs en cours d'exécution
	docker compose ps

.PHONY: shell
shell: ## Ouvre un shell bash dans le conteneur PHP
	$(PHP) bash

## —— Composer ——————————————————————————————————————————————————————————————
.PHONY: install
install: ## Installe les dépendances Composer
	$(PHP) composer install

.PHONY: update
update: ## Met à jour les dépendances Composer
	$(PHP) composer update

.PHONY: require
require: ## Ajoute un package Composer (usage : make require p=nom/package)
	$(PHP) composer require $(p)

.PHONY: require-dev
require-dev: ## Ajoute un package Composer en dev (usage : make require-dev p=nom/package)
	$(PHP) composer require --dev $(p)

## —— Symfony ———————————————————————————————————————————————————————————————
.PHONY: cc
cc: ## Vide le cache Symfony
	$(CONSOLE) cache:clear

.PHONY: warmup
warmup: ## Réchauffe le cache Symfony
	$(CONSOLE) cache:warmup

.PHONY: routes
routes: ## Liste toutes les routes
	$(CONSOLE) debug:router

.PHONY: services
services: ## Liste tous les services du conteneur
	$(CONSOLE) debug:container

.PHONY: maker
maker: ## Lance le maker bundle (usage : make maker c="make:controller NomController")
	$(CONSOLE) $(c)

## —— Base de données ————————————————————————————————————————————————————————
.PHONY: db-create
db-create: ## Crée la base de données
	$(CONSOLE) doctrine:database:create --if-not-exists

.PHONY: db-drop
db-drop: ## Supprime la base de données
	$(CONSOLE) doctrine:database:drop --force --if-exists

.PHONY: db-reset
db-reset: db-drop db-create migrate ## Recrée la base de données et joue les migrations

.PHONY: migrate
migrate: ## Joue toutes les migrations en attente
	$(CONSOLE) doctrine:migrations:migrate --no-interaction

.PHONY: migration
migration: ## Génère une nouvelle migration à partir des entités
	$(CONSOLE) doctrine:migrations:diff

.PHONY: migration-status
migration-status: ## Affiche le statut des migrations
	$(CONSOLE) doctrine:migrations:status

.PHONY: fixtures
fixtures: ## Charge les fixtures (données de test)
	$(CONSOLE) doctrine:fixtures:load --no-interaction

.PHONY: db-reset-fixtures
db-reset-fixtures: db-reset fixtures ## Recrée la BDD, joue les migrations et charge les fixtures

## —— Assets ————————————————————————————————————————————————————————————————
.PHONY: assets
assets: ## Installe les assets (importmap)
	$(CONSOLE) importmap:install

.PHONY: assets-compile
assets-compile: ## Compile les assets pour la production
	$(CONSOLE) asset-map:compile

## —— Tests —————————————————————————————————————————————————————————————————
.PHONY: test
test: ## Lance tous les tests PHPUnit
	$(PHP) php bin/phpunit

.PHONY: test-filter
test-filter: ## Lance un test spécifique (usage : make test-filter f=NomDuTest)
	$(PHP) php bin/phpunit --filter=$(f)

.PHONY: test-coverage
test-coverage: ## Lance les tests avec rapport de couverture HTML (dans var/coverage/)
	$(PHP) php bin/phpunit --coverage-html var/coverage

## —— Qualité de code ————————————————————————————————————————————————————————
.PHONY: lint
lint: ## Vérifie la syntaxe des fichiers Twig, YAML et PHP
	$(CONSOLE) lint:twig templates/
	$(CONSOLE) lint:yaml config/
	$(CONSOLE) lint:container

.PHONY: phpstan
phpstan: ## Lance l'analyse statique PHPStan (si installé)
	$(PHP) vendor/bin/phpstan analyse

## —— Utilitaires ———————————————————————————————————————————————————————————
.PHONY: open
open: ## Ouvre l'application dans le navigateur
	xdg-open http://localhost:8090

.PHONY: open-adminer
open-adminer: ## Ouvre Adminer dans le navigateur
	xdg-open http://localhost:8081

.PHONY: open-mail
open-mail: ## Ouvre Mailpit dans le navigateur
	xdg-open http://localhost:8025
