#!/usr/bin/env bash

header () {
    printf "${LIGHT_GRAY}${1}${NC}\n"
}

info () {
    printf "\n${GREEN}${1}${NC}\n\n"
}

error () {
    printf "\n${RED}${1}${NC}\n\n"
}

comment () {
    printf "\n${YELLOW}${1}${NC}\n\n"
}

no-exec () {
    comment "No-Exec..."
}

enabled () {
    echo " enabled."
}
disabled () {
    echo " disabled."
}

help_message () {
if [[ ${HELP} == 1 ]]
then
    echo "Options:"
    echo "  --help      - show this message"
    [[ $OPTION_ANALYZE ]] && echo "  --analyze   - enable analysis"
    [[ $OPTION_COVERAGE ]] && echo "  --coverage  - enable code coverage"
    [[ $OPTION_ALL ]] && echo "  --all       - enable analysis and code coverage"
    [[ $OPTION_BEAUTY ]] && echo "  --beautify  - enable code beautifier"
    [[ $OPTION_BEAUTY ]] && echo "  --beauty    - same as above"
    [[ $OPTION_PROPAGATE ]] && echo "  --propagate - propagate unrecognized options to underlying script"
    exit 0
fi
}

