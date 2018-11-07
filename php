#!/bin/bash
# Usage: php [options] [path-to-php-script]
# Example: php -a
set -eu

dir="$(dirname "$0")"

. "$dir/.env"

# note: port 9000 should match XDEBUG_REMOTE_PORT in docker-compose.yml
# note: that port must be open on the local machine to allow debugging!
# todo: manage the port via .env, just like the address
# todo: once docker-compose supports -w options, set the working dir to /var/www/mediawiki (or take the from .env).
docker-compose exec "web" php -dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=$IDELOCALHOST $1 ${*:2}
