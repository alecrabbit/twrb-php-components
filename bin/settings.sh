#!/usr/bin/env bash
COVERAGE=0
PROPAGATE=0
ANALYZE=0
EXEC=1
HELP=0

echo "$@"
for arg
do
    case "$arg" in
        --propagate)
            PROPAGATE=1
            ;;
        *)
            ;;
     esac
done

for arg
do
    case "$arg" in
        --help)
            HELP=1
            if [[ ${PROPAGATE} == 1 ]]
                then
                    params+=("$arg")
            fi
            ;;
        --no-exec)
            EXEC=0
            ;;
        --analyze)
            ANALYZE=1
            ;;
        --coverage)
            COVERAGE=1
            ;;
        --propagate)
            PROPAGATE=1
            ;;
        *)
            if [[ ${PROPAGATE} == 1 ]]
                then
                    params+=("$arg")
                else
                    echo "settings.sh: Unknown argument/option ${arg}"
                    exit 0
            fi
            ;;
     esac
done
set -- "${params[@]}"  # overwrites the original positional params
echo "$@"

SOURCE_DIR="src"
PHPSTAN_LEVEL=7
PSALM_CONFIG="./../psalm.xml"
PSALM_LEVEL=3
PHPMETRICS_OUTPUT_DIR="./tests/phpmetrics"
PHPUNIT_COVERAGE_HTML_REPORT="./tests/coverage/html"
PHPUNIT_COVERAGE_CLOVER_REPORT="./tests/coverage/clover.xml"

if [[ ${COVERAGE} == 1 ]]
then
  DOCKER_COMPOSE_FILE="./../docker-compose-xdebug.yml"
else
  DOCKER_COMPOSE_FILE="./../docker-compose.yml"
fi


