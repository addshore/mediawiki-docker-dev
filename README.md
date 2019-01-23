## Instructions

By default the below steps will install MediaWiki at `~/dev/mediawiki`
and start a server for <http://default.web.mw.localhost:8080>.

Many aspect of the container, including the port and MediaWiki path, can be customised
by creating a `local.env` in this directory, in which to override one or more variables
from `default.env`.

### Install

#### 1) Install Docker & Docker Compose

https://docs.docker.com/compose/install/

###### Unix notes

- Use the `docker-ce` package, not the `docker` package (read their install instructions)
- If you want to avoid logging in as root or sudo commands, you will have to add your user to the docker group:
https://askubuntu.com/questions/477551/how-can-i-use-docker-without-sudo#477554
   - This does not mean your containers will not run as root. These are different settings not really used currently by this dev setup.

#### 2) Clone this repository

```
git clone https://github.com/addshore/mediawiki-docker-dev.git
```

#### 3) Clone MediaWiki core & the Vector skin

You can start without the skin but you will find that your MediaWiki install doesn't look very nice.

From [Wikimedia Gerrit](https://gerrit.wikimedia.org/r/#/admin/projects/mediawiki/core):

```
git clone https://gerrit.wikimedia.org/r/mediawiki/core /srv/dev/git/gerrit/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector /srv/dev/git/gerrit/mediawiki/skins/Vector
```

(You can clone your code to somewhere other than `/srv/dev/git/gerrit/mediawiki`. For example, `~/src/mediawiki` but you'll need to follow step 6 carefully.)

#### 4) Run `composer install` for MediaWiki

Either on your host machine or with Docker, inside the `/srv/dev/git/gerrit/mediawiki` directory:

```
docker run -it --rm --user $(id -u):$(id -g) -v ~/.composer:/tmp -v $(pwd):/app docker.io/composer install
```

#### 5) Create a basic LocalSettings.php

The .docker/LocalSettings.php file will exist within the containers running Mediawiki.

Make a LocalSettings.php in the root of the Mediawiki repo containing the following:

```
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
```
When you come to change any MediaWiki settings this is the file you will want to be altering.

For example after install you will probably find you want to load the default skin:
```
wfLoadSkin( 'Vector' );
```

### 6) Configure the environment

Note: If you cloned mediawiki into a directory other than `/srv/dev/git/gerrit/mediawiki` you will need to do this step, otherwise the defaults can likely be used.

Copy the content of `default.env` from the `mediawiki-docker-dev` dir into a new file called `local.env`.

Alter any settings that you want to change, for example the install location of MediaWiki, a directory to a local composer cache, or webserver or php version.

#### 7) Launch the environment

**Create and start the Docker containers:**

This includes setting up a default wiki @ http://default.web.mw.localhost:8080

```
./up
```

**Update your hosts file:**

Add the following to your `/etc/hosts` file:

```
127.0.0.1 default.web.mw.localhost # mediawiki-docker-dev
127.0.0.1 proxy.mw.localhost # mediawiki-docker-dev
127.0.0.1 phpmyadmin.mw.localhost # mediawiki-docker-dev
127.0.0.1 graphite.mw.localhost # mediawiki-docker-dev
```

You can also use the `./hosts-sync` script to try and update it automatically if possible. You may
need to use `sudo ./hosts-sync` instead if the file is not writable by the shell user.

## Commands

The below commands are shell scripts in the mediawiki-docker-dev directory.

For example, the Up command can be invoked as `./up`, and the Bash command as `./bash`, etc.

To easily invoke these while working in another directory (e.g. mediawiki/core, or an extension) you can add a small bash alias to your `bashrc` file. For example:

```bash
alias mw-docker-dev='_(){ (cd /$GITPATH/github/addshore/mediawiki-docker-dev; ./$@) ;}; _'
```

The below documentation assumes this alias in examples, but each of these also works directly. Instead of `mw-docker-dev start` you would run `./start` from your Terminal tab for mw-docker-dev.

### Up

Create and start containers.

This includes setting up a default wiki @ http://default.web.mw.localhost:8080 with an "Admin" user that has password "dockerpass".

You can choose the spec of the system that the up command will set up by using a custom .env file called local.env and customizing the variables.

```
mw-docker-dev up
```

### Stop

Shuts down the containers. Databases and other volumes persist.

```
mw-docker-dev stop
```

### Start

Start (or restart) the containers, if things have already been created using `./up`.

```
mw-docker-dev start
```

### Down

Stop and delete the containers. Also removes databases and volumes.

```
mw-docker-dev down
```

### Bash

Run commands on the webserver.

If the containers are running you can use `./bash` to open up an interactive shell on the webserver.

This can be used to run PHPUnit tests, maintenance scripts, etc.

```
mw-docker-dev bash
```

### Add site

You can add a new site by subdomain name using the ./addsite command

```
mw-docker-dev addsite enwiki
```

### Hosts file sync

Check whether the hosts file contains all needed entries, and if not,
shows which entries need to be added, and also tries to add them automatically
if possible.

```
mw-docker-dev hosts-sync
```

### Update a wiki

Run `git pull` in your the relevant Git repositories for MediaWiki core
and extensions.

If you need to apply schema changes after updating MediaWiki, or after
installing additional extensions, you can follow the regular MediaWiki
instructions. Just make sure you're on the web server when doing so.

For example:

```
$ mw-docker-dev bash

root@web:/var/www/mediawiki# php maintenance/update.php
```

### PHPUnit

Be sure to set `default` (the wiki db), this is a multi-wiki environment.

For example:

```
mw-docker-dev phpunit-file default extensions/FileImporter/tests/phpunit
```

See also <https://www.mediawiki.org/wiki/Manual:PHP_unit_testing>

### QUnit

To run the QUnit tests from the browser, use [Special:JavaScriptTest](http://default.web.mw.localhost:8080/index.php?title=Special:JavaScriptTest).

See also <https://www.mediawiki.org/wiki/Manual:JavaScript_unit_testing>.

To run QUnit from the command-line, make sure you have [Node.js v4 or later](https://nodejs.org/) installed on the host, and set the following environment variables:

```
export MW_SERVER='http://default.web.mw.localhost:8080'
export MW_SCRIPT_PATH='/mediawiki'
```

```
$ cd ~/dev/mediawiki
$ npm install
$ npm run qunit
```

## Access

 - [Default MediaWiki Site](http://default.web.mw.localhost:8080)
 - [Graphite](http://graphite.mw.localhost:8080)
 - [PhpMyAdmin](http://phpmyadmin.mw.localhost:8080)

## Debugging

While using PHP you can use remote xdebug debugging.

To do so you need to set `IDELOCALHOST` in you local.env file to the IP of your local machine (where you run your IDE) as it appears to docker. Note with Docker for Mac, you can use `IDELOCALHOST=host.docker.internal`.

xdebug connections will then be sent to this IP address on port 9000.

## TODO

 - FIX HHVM strict mode
   - Strict Warning: It is not safe to rely on the system's timezone settings. Please use the date.timezone setting, the TZ environment variable or the date_default_timezone_set() function.
 - Statsv endpoint
 - Setup awesome hosts file additions & removals
