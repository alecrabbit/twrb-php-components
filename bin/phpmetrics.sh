#!/usr/bin/env bash

script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

OPTION_PROPAGATE=1

help_message


info "PhpMetrics..."
if [[ ${EXEC} == 1  ]]
then
    if [[ -z "$@" ]]
    then
        docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpmetrics --report-html="${PHPMETRICS_OUTPUT_DIR}" .
    else
        docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpmetrics "$@"
    fi
else
  no-exec
fi

