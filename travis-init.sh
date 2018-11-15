#!/bin/bash
set -e
set -o pipefail

if [[ "$TRAVIS_PHP_VERSION" != "hhvm" &&
      "$TRAVIS_PHP_VERSION" != "hhvm-nightly" ]]; then

    # install 'event' and 'ev' PHP extension
    if [[ "$TRAVIS_PHP_VERSION" != "5.3" ]]; then
        echo "yes" | pecl install event
        echo "yes" | pecl install ev
    fi
fi