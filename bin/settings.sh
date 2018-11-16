#!/usr/bin/env bash

SOURCE_DIR="src";
PHPSTAN_LEVEL=7;
PSALM_LEVEL=3;
PSALM_CONFIG="./psalm.xml";
PHPMETRICS_OUTPUT_DIR="./tests/phpmetrics"
PHPUNIT_COVERAGE_HTML_REPORT="./tests/coverage/html"
PHPUNIT_COVERAGE_CLOVER_REPORT="./tests/coverage/clover.xml"
DOCKER_COMPOSE_FILE="./../docker-compose-xdebug.yml"

