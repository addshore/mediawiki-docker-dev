#!/usr/bin/env bash
set -eu

mkdir -p "$INSTALL_DIR"
[[ ! -d $HOME/.composer ]] && mkdir $HOME/.composer


DOCKER_MW_PATH="$INSTALL_DIR"/mediawiki

git clone --depth 1 https://gerrit.wikimedia.org/r/mediawiki/core "$DOCKER_MW_PATH"
git clone --depth 1 https://gerrit.wikimedia.org/r/mediawiki/skins/Vector "$DOCKER_MW_PATH"/skins/Vector

echo "DOCKER_MW_PATH=$DOCKER_MW_PATH" >> local.env

cd "$DOCKER_MW_PATH"
docker run -it --rm --user $(id -u):$(id -g) -v $HOME/.composer:/tmp -v $(pwd):/app composer install --ignore-platform-reqs
touch LocalSettings.php
cat > LocalSettings.php <<EOL
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
wfLoadSkin( 'Vector' );
EOL
