#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

OPTION_PROPAGATE=1

help_message

info "PHP Code Sniffer Beautifier..."
if [[ ${EXEC} == 1 ]] && [[ ${BEAUTY} == 1 ]]
then
    if [[ -z "$@" ]]
    then
        docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpcbf
    else
        docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpcbf "$@"
    fi
else
  no-exec
fi
