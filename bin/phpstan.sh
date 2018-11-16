#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

info "PHPStan..."
if [[ ${EXEC} == 1 ]]
then
    if [[ -z "$@" ]]
    then
        docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpstan analyze ${SOURCE_DIR} --level=${PHPSTAN_LEVEL}
    else
        docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpstan "$@"
    fi
else
  no-exec
fi
