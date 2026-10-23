.PHONY: install install-dev update update-dev phpcs php-lint phpstan-level-5 phpstan-level-8 phpstan-baseline phpstan-ci

# Affiche la liste des commandes disponibles
help:
	@echo "Syntax: [make target] where target is in this list:"
	@awk '/^#/{c=substr($$0,3);next}c&&/^[[:alpha:]][[:alnum:]_-]+:/{print substr($$1,1,index($$1,":")),c}1{c=0}' $(MAKEFILE_LIST) | column -s: -t

# Installe les dépendances de production
install:
	composer install --no-dev

# Installe toutes les dépendances (dev incluses)
install-dev:
	composer install

# Met à jour les dépendances de production
update:
	composer update --no-dev

# Met à jour toutes les dépendances (dev incluses)
update-dev:
	composer update

# Vérifie la compatibilité PHP avec PHPCompatibility
phpcs:
	vendor/bin/phpcs

# Vérifie la syntaxe de tous les fichiers PHP
php-lint:
	./scripts/php-lint.sh

# Analyse PHPStan niveau 5, sans baseline
phpstan-level-5:
	vendor/bin/phpstan analyse --no-progress --level=5 --configuration=phpstan.neon cron include public

# Analyse PHPStan niveau 8 (plus strict), sans baseline
phpstan-level-8:
	vendor/bin/phpstan analyse --no-progress --error-format=raw --level=8 --configuration=phpstan.neon cron include public

# Régénère la baseline PHPStan (erreurs existantes ignorées)
phpstan-baseline:
	vendor/bin/phpstan analyse --no-progress --error-format=raw --configuration=phpstan.neon --generate-baseline phpstan-baseline.neon --allow-empty-baseline

# Analyse PHPStan comme en CI, avec la baseline
phpstan-ci:
	vendor/bin/phpstan analyse --no-progress --error-format=github --configuration=phpstan-ci.neon
