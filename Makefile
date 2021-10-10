up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up hi-init

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

hi-init: hi-composer-install

hi-composer-install:
	docker-compose run --rm hi-php-cli composer install
# 	docker-compose run --rm hi-node yarn start

frontend-yarn-install:
	docker-compose run --rm hi-node-cli yarn install