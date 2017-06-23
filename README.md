## Instructions

If you don't want to use the default port of 8080 and the default mediawiki path of ~/dev/git/gerrit/mediawiki then please just change the .env file for now....

### Install

#### 1) Install Docker & Docker Compose

https://docs.docker.com/compose/install/

If you don't want to run the container as root, you will have to add your user to the docker group:
https://askubuntu.com/questions/477551/how-can-i-use-docker-without-sudo#477554

#### 2) Clone this repository

```
git clone https://github.com/addshore/mediawiki-docker-dev.git
```

#### 3) Clone MediaWiki Core & the Vector Skin

From [Wikimedia Gerrit](https://gerrit.wikimedia.org/r/#/admin/projects/mediawiki/core):

```
git clone https://gerrit.wikimedia.org/r/mediawiki/core ~/dev/gerrit/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector ~/dev/gerrit/mediawiki/skins/Vector
```

Or from [Github Mirror](https://github.com/wikimedia/mediawiki) (often quicker):

```
git clone https://github.com/wikimedia/mediawiki.git ~/dev/gerrit/mediawiki
git clone https://github.com/wikimedia/mediawiki-skins-Vector.git ~/dev/gerrit/mediawiki/skins/Vector

# You can then set the remote to point back to gerrit:

git remote set-url origin https://gerrit.wikimedia.org/r/mediawiki/core
git remote set-url origin https://gerrit.wikimedia.org/r/mediawiki/skins/Vector
```

#### 4) Create a basic LocalSettings.php

The .docker/LocalSettings.php file will exist within the containers running Mediawiki.

Make a LocalSettings.php in the root of the Mediawiki repo containing the following:

```
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
```

#### 5) Update your local hosts file

You will need to check the ./config/local/hosts file and add these to your local hosts file.

### Operation

You need to populate your hosts file to get the most out of this docker stuff (see above).

**To set up the containers**:

This includes setting up a default wiki @ https://default.web.mw.dev:8080

You can choose the spec of the system that the up command will set up by using a custom .env file called local.env and customizing the variables.

```
./up
```

**To stop the containers**:

Databases persist.

```
./stop
```

**To restart the containers**:

If things have already been setup using up.

```
./start
```

**To tear down the containers**:

Removes databases.

```
./down
```

**Run commands on the webserver**:

If the containers are running you can use the ./bash script to open up a interactive shell on the webserver.

This can be used to run tests, maintenance scripts etc.

```
./bash
```

**Add a new site**:

You can add a new site by name using the ./addsite command

```
./addsite enwiki
```

**Run tests**:

```
./phpunit --wiki default //var/www/mediawiki/extensions/FileImporter/tests/phpunit
```

### Access

 - [Default MediaWiki Site](http://default.web.mw.dev:8080)
 - [Graphite](http://graphite.mw.dev:8080)
 - [PhpMyAdmin](http://phpmyadmin.mw.dev:8080)

### Debugging

While using PHP you can use remote xdebug debugging.

To do so you need to set IDELOCALHOST in you local.env file to the IP of your local machine (where you run your IDE) as it appears to docker.

xdbeug connecitons will then be sent to this IP address on port 9000.

### TODO

 - FIX HHVM strict mode
   - Strict Warning: It is not safe to rely on the system's timezone settings. Please use the date.timezone setting, the TZ environment variable or the date_default_timezone_set() function.
 - Statsv endpoint
 - Setup awesome hosts file additions & removals
 - Should be able to run with no internet (so do something about composer install step))