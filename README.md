## Instructions

If you don't want to use the default port of 8080 and the default mediawiki path of ~/dev/git/gerrit/mediawiki then set `DOCKER_MW_PATH` or `DOCKER_MW_PORT` to something else in a `local.env` file.

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
git clone https://gerrit.wikimedia.org/r/mediawiki/core ~/dev/git/gerrit/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector ~/dev/git/gerrit/mediawiki/skins/Vector
```

Or from [Github Mirror](https://github.com/wikimedia/mediawiki) (often quicker):

```
git clone https://github.com/wikimedia/mediawiki.git ~/dev/git/gerrit/mediawiki
git clone https://github.com/wikimedia/mediawiki-skins-Vector.git ~/dev/git/gerrit/mediawiki/skins/Vector

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

#### 5) Run various commands to interact with the environment

You can setup a small bash alias to make running the various commands much easier.
An example is provided below:

```bash
alias mw-docker-dev='_(){ (cd /$GITPATH/github/addshore/mediawiki-docker-dev; ./$@) ;}; _'
```

Without such a bash alias you will have the run the scripts from within the mediawiki-docker-dev directory itself.

**To set up the containers**:

This includes setting up a default wiki @ http://default.web.mw.localhost:8080

You can choose the spec of the system that the up command will set up by using a custom .env file called local.env and customizing the variables.

```
mw-docker-dev up
```

**To stop the containers**:

Databases persist.

```
mw-docker-dev stop
```

**To restart the containers**:

If things have already been setup using up.

```
mw-docker-dev start
```

**To tear down the containers**:

Removes databases.

```
mw-docker-dev down
```

**Run commands on the webserver**:

If the containers are running you can use the ./bash script to open up a interactive shell on the webserver.

This can be used to run tests, maintenance scripts etc.

```
mw-docker-dev bash
```

**Add a new site**:

You can add a new site by name using the ./addsite command

```
mw-docker-dev addsite enwiki
```

**Run tests**:

```
mw-docker-dev phpunit --wiki default //var/www/mediawiki/extensions/FileImporter/tests/phpunit
```

### Access

 - [Default MediaWiki Site](http://default.web.mw.localhost:8080)
 - [Graphite](http://graphite.mw.localhost:8080)
 - [PhpMyAdmin](http://phpmyadmin.mw.localhost:8080)

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
