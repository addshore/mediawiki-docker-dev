#!/usr/bin/env bash

mkdir -p ~/src/
git clone https://gerrit.wikimedia.org/r/mediawiki/core ~/src/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector ~/src/mediawiki/skins/Vector
cd ~/src/mediawiki
docker run -it --rm --user $(id -u):$(id -g) -v ~/.composer:/tmp -v $(pwd):/app docker.io/composer install
touch LocalSettings.php
cat > LocalSettings.php <<EOL
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
wfLoadSkin( 'Vector' );
EOL
