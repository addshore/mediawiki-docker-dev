#!/bin/bash
# Usage: php [options] [path-to-php-script]
# Example: php -a
set -eu

dir="$(dirname "$0")"
cd "$dir"

# todo: once docker-compose supports the -w options, set the working dir
# to /var/www/mediawiki (or take that from .env).
docker-compose exec -T "web" php "$@"
