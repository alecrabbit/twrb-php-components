#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

info "PHPStan..."
docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpstan analyze ${SOURCE_DIR} --level=${PHPSTAN_LEVEL}
