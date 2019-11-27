#!/usr/bin/env bash
set -eu

mkdir -p "$INSTALL_DIR"
[[ ! -d $HOME/.composer ]] && mkdir $HOME/.composer

git clone --depth 1 https://gerrit.wikimedia.org/r/mediawiki/core "$INSTALL_DIR"/mediawiki
git clone --depth 1 https://gerrit.wikimedia.org/r/mediawiki/skins/Vector "$INSTALL_DIR"/mediawiki/skins/Vector

cd "$INSTALL_DIR"/mediawiki
docker run -it --rm --user $(id -u):$(id -g) -v $HOME/.composer:/tmp -v $(pwd):/app composer install --ignore-platform-reqs
touch LocalSettings.php
cat > LocalSettings.php <<EOL
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
wfLoadSkin( 'Vector' );
EOL
chmod 777 LocalSettings.php
