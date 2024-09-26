##################
# Variables
##################

DOCKER_COMPOSE = docker compose -f ./docker/docker-compose.yml --env-file ./docker/.env
DOCKER_COMPOSE_PHP_FPM_EXEC = ${DOCKER_COMPOSE} exec -u www-data php-fpm

##################
# Docker compose
##################

init:
	make dc_build
	make dc_up
	make composer_install

dc_build:
	${DOCKER_COMPOSE} build

dc_start:
	${DOCKER_COMPOSE} start

dc_stop:
	${DOCKER_COMPOSE} stop

dc_up:
	${DOCKER_COMPOSE} up -d --remove-orphans

dc_ps:
	${DOCKER_COMPOSE} ps

dc_logs:
	${DOCKER_COMPOSE} logs -f

dc_down:
	${DOCKER_COMPOSE} down -v --rmi=all --remove-orphans

dc_restart:
	make dc_stop dc_start


##################
# App
##################

app_bash:
	${DOCKER_COMPOSE} exec -u www-data php-fpm bash
composer_install:
	docker compose -f ./docker/docker-compose.yml exec -u www-data php-fpm composer install --no-scripts --prefer-dist
