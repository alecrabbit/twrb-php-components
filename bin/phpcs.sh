#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

info "PHP Code Sniffer..."
if [[ ${EXEC} == 1 ]]
then
  docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpcs
else
  no-exec
fi
