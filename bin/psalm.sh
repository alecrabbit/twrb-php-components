#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. settings.sh
. functions.sh

if [[ -e ${PSALM_CONFIG} ]]
then
  comment "Psalm config found..."
else
  comment "Creating psalm config..."
#  docker-compose -f ${DOCKER_COMPOSE_FILE} exec app psalm --init src 3
  docker-compose -f ${DOCKER_COMPOSE_FILE} exec app psalm --init ${SOURCE_DIR} ${PSALM_LEVEL}
fi

info "Psalm..."

docker-compose -f ${DOCKER_COMPOSE_FILE} exec app psalm
