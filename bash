#!/bin/sh
set -eu

echo "Running bash on the webserver"

docker-compose exec "web" sh -c "cd /var/www/mediawiki;bash"
