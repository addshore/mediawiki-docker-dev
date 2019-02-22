#!/usr/bin/env bash
set -eu

mkdir -p "$INSTALL_DIR"
git clone https://gerrit.wikimedia.org/r/mediawiki/core "$INSTALL_DIR"/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector "$INSTALL_DIR"/mediawiki/skins/Vector
cd "$INSTALL_DIR"/mediawiki
docker run -it --rm --user $(id -u):$(id -g) -v ~/.composer:/tmp -v $(pwd):/app docker.io/composer install
touch LocalSettings.php
cat > LocalSettings.php <<EOL
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
wfLoadSkin( 'Vector' );
EOL
