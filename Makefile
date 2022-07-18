# See:
# http://www.gnu.org/software/make/manual/make.html
# http://linuxlib.ru/prog/make_379_manual.html

EXEC_PHP      = php
EXEC_PHP_ROOT = --user=root php
ARTISAN       = $(EXEC_PHP) artisan
COMPOSER      = composer
.PHONY : rebuild

SPECTRAL_IMAGE_NAME   = stoplight/spectral:5.9.1
SPECTRAL_RULESET_PATH = ./api/spectral.yaml


# This allows us to accept extra arguments
%:
	@:
args = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

serve:
	$(ARTISAN) serve

copy-env-if-not-exists:
	test -f .env || cp .env.example .env

cc:
	$(ARTISAN) opt:clear

composer:
	$(COMPOSER) $(call args)

console:
	$(ARTISAN) $(call args)

qa: openapi-validate phpstan phpunit

openapi-validate:
	docker run --rm \
		-v $(shell git rev-parse --show-toplevel):/root \
		-w /root $(SPECTRAL_IMAGE_NAME) \
		lint --fail-severity=warn --ruleset=$(SPECTRAL_RULESET_PATH) --verbose openapi.yaml

phpstan:
	$(EXEC_PHP) vendor/bin/phpstan analyse

phpunit:
	$(EXEC_PHP) vendor/bin/phpunit

phpunit-method:
	$(EXEC_PHP) vendor/bin/phpunit --filter $(call args)

phpunit-file:
	$(EXEC_PHP) vendor/bin/phpunit $(call args)

phpunit-coverage:
	$(EXEC_PHP) php -dxdebug.mode=coverage vendor/bin/phpunit --coverage-html coverage

migration:
	$(ARTISAN) make:migration $(call args)

migrate:
	$(ARTISAN) migrate

migrate-rollback:
	$(ARTISAN) migrate:rollback
