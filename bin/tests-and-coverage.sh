#!/usr/bin/env bash
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh

header "Tests and coverage script"
comment "Restarting container..."

docker-compose down && docker-compose -f ${DOCKER_COMPOSE_FILE} up -d

info "Processing..."

info "PhpUnit..."

docker-compose -f ${DOCKER_COMPOSE_FILE} exec app phpunit --coverage-html ${PHPUNIT_COVERAGE_HTML_REPORT} --coverage-text  \
--coverage-clover ${PHPUNIT_COVERAGE_CLOVER_REPORT}

./phpmetrics.sh

./phpstan.sh

./psalm.sh

