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
