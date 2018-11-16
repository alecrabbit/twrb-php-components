#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

info "PhpMetrics..."
docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpmetrics --report-html="${PHPMETRICS_OUTPUT_DIR}" .
