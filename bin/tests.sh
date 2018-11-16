#!/usr/bin/env bash
start_time=`date +%s`
script_dir="$(dirname "$0")"
cd ${script_dir}

. imports.sh "$@"


if [[ ${HELP} == 1 ]]
then
    ./help.sh
    exit 0
fi
header "Tests and coverage script"
if [[ ${EXEC} == 0 ]]
then
    echo "No-Exec selected."
else
    printf "Analysis"
    if [[ ${ANALYZE} == 1 ]]
    then
        enabled
    else
        disabled
    fi
    printf "Coverage"
    if [[ ${COVERAGE} == 1 ]]
    then
        enabled
    else
        disabled
    fi

fi


./restart-container.sh "$@"

info "Processing..."

./phpunit.sh "$@"

if [[ ${ANALYZE} == 1 ]]
then
  ./phpmetrics.sh "$@"

  ./phpstan.sh "$@"

  ./psalm.sh "$@"

  ./phpcs.sh "$@"
fi


end_time=`date +%s`
run_time=$((end_time-start_time))
info "Executed in ${run_time}s\nBye!"
exit 0
