up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up api-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

update-deps: api-composer-update frontend-yarn-upgrade restart

api-init: api-composer-install

api-composer-install:
	docker-compose run --rm api-php-cli composer install

api-composer-update:
	docker-compose run --rm api-php-cli composer update

api-migrations:
	docker-compose run --rm api-php-cli bin/console doctrine:migrations:migrate --no-interaction

api-composer-bash:
	docker-compose run --rm --env "COMPOSER_AUTH={\"github-oauth\": {\"github.com\": \"`composer config -g github-oauth.github.com`\"} }" api-php-cli bash

api-test-integration:
	docker-compose run --rm api-php-cli bin/phpunit tests/Integration

eav-populate:
	docker-compose run --rm api-php-cli bin/console eav:populate

frontend-yarn-upgrade:
	docker-compose run --rm frontend-node yarn upgrade