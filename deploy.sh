#!/bin/sh
set -eu

export FILE_DOCKER_COMPOSE=docker-compose.prod.yaml

echo "*** Building image on production mode ***"
docker-compose --file ${FILE_DOCKER_COMPOSE} build
echo "*** The image has been created on production mode ***"

echo "*** Starting deploy on production mode ***"
if [ ! $(docker-compose --file ${FILE_DOCKER_COMPOSE} down -v) = 0 ]; then
  echo "*** Container stopped and removed successfully ***"
else
  echo "*** Containers are not running ***"
fi

echo "*** Starting containers on production mode ***"
docker-compose --file ${FILE_DOCKER_COMPOSE} up -d
echo "*** Containers are running on production mode ***"
docker-compose --file ${FILE_DOCKER_COMPOSE} ps

echo "*** Starting create db in production mode ***"
docker-compose --file ${FILE_DOCKER_COMPOSE} exec app php bin/console doctrine:database:create --if-not-exists
echo "*** Command executed successfully ***"

if [ "ls migrations/ | wc -l" > 1 ]; then
  echo "*** Run migrations db ***"
  docker-compose --file ${FILE_DOCKER_COMPOSE} exec app php bin/console doctrine:migration:migrate --no-interaction
  echo "*** Migrations executed in database ***"
else
  echo "*** Do not migrations to execute ***"
fi

sudo chmod -R 777 var/
