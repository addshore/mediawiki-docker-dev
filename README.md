# Instructions

## Installation

By default the below steps will install MediaWiki at `~/src/mediawiki`
and start a server for <http://default.web.mw.localhost:8080>.

Many aspects of the container, including the port and MediaWiki path, can be customised
by creating a `local.env` in the root directory of this project, in which to override one or more variables
from `default.env`.

### Requirements

#### 1) Install Docker & Docker Compose

[Get Docker](https://docs.docker.com/install/)

##### Unix notes

- Use the `docker-ce` package, not the `docker` package.
- If you want to avoid logging in as root or sudo commands, you will have to add your user to the docker group:
See [How can I use Docker without sudo](https://askubuntu.com/questions/477551/how-can-i-use-docker-without-sudo#477554)
  - This does not mean your containers will not run as root.

#### 2) Install Docker Compose

[Docker Compose Installation](https://docs.docker.com/compose/install/)

#### 3) Install git

[Getting Started Installing Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)

#### 4) Install PHP CLI

[Installation and Configuration](https://www.php.net/manual/en/install.php)

#### 5) Clone this repository

```bash
git clone https://github.com/addshore/mediawiki-docker-dev.git
```

**TODO also you need to composer install in the control directory currently.**
**TODO also you need to copy default.env into a local.env file!**

### Alias for your pleasure (highly recommended)

Create an alias like this so that you can run the command from anywhere.

```bash
alias mwdd='_(){ (export MWDD_S_DIR=$(pwd);cd ~/mwdd; ./mwdd $@) ;}; _'
```

[Maybe you want to create a permanent alias](https://askubuntu.com/a/17538/1066974).

The MWDD_S_DIR variable is used by the environment to determine the context you might want to run commands in.

### Automatic Code fetch

There is a setup script that you can run to fetch MediaWiki code once you have the prerequisites.
```bash
mwdd mw:getCode
```

This will pull the code from Gerrit, and do a composer install.

### Manual Code fetch

#### 1) Clone MediaWiki core & the Vector skin

From [Wikimedia Gerrit](https://gerrit.wikimedia.org/r/#/admin/projects/mediawiki/core):

```bash
git clone https://gerrit.wikimedia.org/r/mediawiki/core ~/src/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector ~/src/mediawiki/skins/Vector
```

#### 2) Run `composer install` for MediaWiki

Either on your host machine inside the `~/src/mediawiki` directory (you need [composer locally](https://getcomposer.org/download/)):

```bash
composer install
```

or with Docker on Linux,

```bash
docker run -it --rm --user $(id -u):$(id -g) -v ~/.composer:/tmp -v $(pwd):/app docker.io/composer install
```

or with Docker on Windows with bash,

```bash
docker run -it --rm -v /$HOME/.composer:/tmp -v /$PWD:/app docker.io/composer install
```

or with Docker on Windows with cmd,

```cmd
docker run -it --rm -v %HOMEDRIVE%%HOMEPATH%\.composer:/tmp -v %CD%:/app docker.io/composer install
```

#### 3) Create a basic LocalSettings.php

A `.docker/LocalSettings.php` file exists within the containers running MediaWiki.
Your `LocalSettings.php` file must load it.

Make a `LocalSettings.php` in the root of the MediaWiki repo containing the following:

```php
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
```

When you come to change any MediaWiki settings this `LocalSettings.php` is the file you will want to be altering.

For example after install you will probably find you want to load the default skin:

```php
wfLoadSkin( 'Vector' );
```

### Configuration

Copy the content of `default.env` from the `mediawiki-docker-dev` dir into a new file called `local.env`.

```bash
cp default.env local.env
```

Alter any settings that you want to change, for example, the install directory of MediaWiki.

```bash
sed 's/\/srv\/dev\/git\/gerrit\/mediawiki/~\/src\/mediawiki/g' local.env > local.env
```

Under section `# Location of the MediaWiki repo on your machine` set the **full** path to the mediawiki you cloned from Gerrit.
Under section `# PHP Runtime version` set the php version that your mediawiki needs to work with. Setting `RUNTIMEVERSION` to `'latest'` doesn't always work. You might want to specify the php version exactly, e.g. `RUNTIMEVERSION=7.3`.

### Create the base environment

TBA details about the options of doing everything locally with php or via docker-compose..

**Create the base environment:**

By default the mwdd commands will run using php on the host:
```bash
./mwdd base:create
```

There is an experimental version that would run everything in docker containers, but it doesn't fully work yet (in development).

```bash
MWDD_ENV=dc ./mwdd base:create
```

#### Configure name resolution

In order to access the services by name from your docker host system, there are two options:

##### Option a) Via /etc/hosts

You can use the `./hosts-sync` script to try and update it automatically if possible.
You may need to use `sudo ./hosts-sync` instead, if the file is not writable by your shell user.

Or you will need to manually add the services from the `.hosts` file into your machine `hosts` file.

##### Option b) Via [DPS](http://mageddo.github.io/dns-proxy-server/latest/en/)

**NOTE: untested in v1....**

You can use DPS to resolve the container names from your host system.
By default, this feature is disabled.
To enable it, create  a `base.override.yml` file with the following content:
```text
version: '2.2'

services:
  dps:
    volumes:
      - /etc/resolv.conf:/etc/resolv.conf
```
This will allow DPS to modify your `/etc/resolv.conf` and set itself as your system's primary DNS server.

### Advanced interactions with the environment

TBA content

#### Tools

Your best bet is to look at the help output of the command to find tools...

##### MediaWiki PhpUnit

```bash
./mwdd mw:phpunit tests/phpunit/includes/StatusTest.php
```

##### MediaWiki Composer

```bash
./mwdd mw:composer info
```

 - Currently a bit slow (currently runs up each time).
 - Also needs to use a locally stored composer package cache!

##### MediaWiki Fresh

```bash
./mwdd mw:fresh npm ci
```

#### Other Services

You can check the states of various services provided by this environment at:

http://default.web.mw.localhost:8080/mediawiki/index.php?title=Special:Mwdd

##### Redis

Redis can be used for MediaWiki caching

```bash
./mwdd redis:create
```

A redis [object cache](https://www.mediawiki.org/wiki/Manual:$wgObjectCaches) will automatically be created, but will not automaticaly be used by MediaWiki.
```php
$wgObjectCaches['redis'] = [
	'class' => 'RedisBagOStuff',
	'servers' => [ 'redis:6379' ],
];
```

You might want to consider using it as the [main cache](https://www.mediawiki.org/wiki/Manual:$wgSessionCacheType):

```php
$wgMainCacheType = 'redis';
```

You might consider running redis-cli to see the data stored in redis:
```bash
./mwdd redis:cli

127.0.0.1:6379> KEYS *
(empty list or set)
```

##### DB Replication

```bash
./mwdd db:replica:create
```

MediaWiki will automatically start reading from the second DB server.

##### Graphite & Statsd


```bash
./mwdd statsd:create
```

When this service is running $wgStatsdServer will automaticaly be configred to point to it.


# TODO fix everything below here

# This is all still for the "old" setup


The below commands are shell scripts in the mediawiki-docker-dev directory.

For example, the Up command can be invoked as `./create`, and the Bash command as `./bash`, etc.

To easily invoke these while working in another directory (e.g. mediawiki/core, or an extension) you can add a small bash alias to your `bashrc` file. For example:

```bash
alias mw-docker-dev='_(){ (cd /$GITPATH/github/addshore/mediawiki-docker-dev; ./$@) ;}; _'
```

The below documentation assumes this alias in examples, but each of these also works directly. Instead of `mw-docker-dev start` you would run `./start` from your Terminal tab for mw-docker-dev.

### Create

Create and start containers.

This includes installing a default wiki at [http://default.web.mw.localhost:8080](http://default.web.mw.localhost:8080) with an "Admin" user that has password "dockerpass".

The spec of the system that this command will create is based on environment variables. The default spec resides in `default.env`. You can customize these variable from a file called `local.env`, which you may create in this directory.

```bash
mw-docker-dev create
```

### Suspend

Shut down the containers. Databases and other volumes persist. See also: [Resume](#Resume), [Destroy](#Destroy).

```bash
mw-docker-dev suspend
```

### Resume

Start (or restart) the containers. See also: [Suspend](#Suspend).

```bash
mw-docker-dev resume
```

### Destroy

Shut down the containers, and destroy them. Also deletes databases and volumes.

```bash
mw-docker-dev destroy
```

### Bash

Run commands on the webserver.

If the containers are running you can use `./bash` to open an interactive shell on the webserver.

This can be used to run PHPUnit tests, maintenance scripts, etc.

```bash
mw-docker-dev bash
```

### Add site

You can add a new site by subdomain name using the ./addsite command

```bash
mw-docker-dev addsite enwiki
```

### Hosts file sync

Check whether the hosts file contains all needed entries, and if not,
shows which entries need to be added, and also tries to add them automatically
if possible.

```bash
mw-docker-dev hosts-sync
```

### Update a wiki

Run `git pull` in your the relevant Git repositories for MediaWiki core
and extensions.

If you need to apply schema changes after updating MediaWiki, or after
installing additional extensions, you can follow the regular MediaWiki
instructions. Just make sure you're on the web server when doing so.

For example:

```bash
$ mw-docker-dev bash

root@web:/var/www/mediawiki# php maintenance/update.php
```

### PHPUnit

Be sure to set `default` (the wiki db), this is a multi-wiki environment.

For example:

```bash
mw-docker-dev phpunit-file default extensions/FileImporter/tests/phpunit
```

See also <https://www.mediawiki.org/wiki/Manual:PHP_unit_testing>

### QUnit

To run the QUnit tests from the browser, use [Special:JavaScriptTest](http://default.web.mw.localhost:8080/index.php?title=Special:JavaScriptTest).

See also <https://www.mediawiki.org/wiki/Manual:JavaScript_unit_testing>.

To run QUnit from the command-line, make sure you have [Node.js v4 or later](https://nodejs.org/) installed on the host, and set the following environment variables:

```bash
export MW_SERVER='http://default.web.mw.localhost:8080'
export MW_SCRIPT_PATH='/mediawiki'
```

```bash
cd ~/src/mediawiki
npm install
npm run qunit
```

### Composer commands (linting and tests etc.)

The example below runs linting on the WikibaseLexeme extension using a composer command.

Run from the mediawiki-docker-dev directory:
```
docker-compose exec "web" bash -c "cd /var/www/mediawiki/extensions/WikibaseLexeme; composer --ansi test"
```

## Access

- [Default MediaWiki Site](http://default.web.mw.localhost:8080)
- [Graphite](http://graphite.mw.localhost:8080)
- [PhpMyAdmin](http://phpmyadmin.mw.localhost:8080)

## Debugging

While using PHP you can use remote xdebug debugging.

To do so you need to set `IDELOCALHOST` in you local.env file to the IP of your local machine (where you run your IDE) as it appears to docker. Note with Docker for Mac, you can use `IDELOCALHOST=host.docker.internal`.

xdebug connections will then be sent to this IP address on port 9000.

Verify in php.ini that `xdebug_remote_host` is set to the value of `IDELOCALHOST` and `xdebug_remote_port` is 9000. (You may need to destroy and create your containers)

If using Visual Studio Code editor, add the following to your `launch.json`.

```json
"pathMappings": {
    "/var/www/mediawiki": "${workspaceFolder}"
}
```

## Overriding / Extending

You can add additional services, or modify current services, by creating a `docker-compose.override.yml` file ([docs](https://docs.docker.com/compose/extends/)). For example, to add a Redis service, add these contents to `docker-compose.override.yml`:

```yaml
version: '2'
services:
  redis:
    image: redis
```

To modify a current service, for example to [try a different volume caching for macOS](https://docs.docker.com/docker-for-mac/osxfs-caching/) like `:delegated` instead of `:cached` ([file reference](https://docs.docker.com/compose/compose-file/#volumes)):

```yaml
version: '2'
services:
  web:
    volumes:
      - "${DOCKER_MW_PATH}:/var/www/mediawiki:delegated"
```

Note that the other volumes for the `web` service will be merged, so you don't need to specify every volume mapping from the main `docker-compose.yml` file in your `docker-compose.override.yml` file.

## Troubleshooting

### MediaWiki

**Error: 145 Table './default/searchindex' is marked as crashed and should be repaired (db-master)**

For example when running unit tests you might see:

```
Wikimedia\Rdbms\DBQueryError from line 1626 of /var/www/mediawiki/includes/libs/rdbms/database/Database.php: A database query error has occurred. Did you forget to run your application's database schema updater after upgrading?
Query: CREATE TEMPORARY  TABLE `unittest_searchindex` (LIKE `searchindex`)
Function: Wikimedia\Rdbms\DatabaseMysqlBase::duplicateTableStructure
Error: 145 Table './default/searchindex' is marked as crashed and should be repaired (db-master)
```

Running the following maintenance script will fix the issue:

```php ./maintenance/rebuildtextindex.php --wiki default```

### Windows

**/usr/bin/env: ‘bash\r’: No such file or directory**

https://stackoverflow.com/questions/29045140/env-bash-r-no-such-file-or-directory

## TODO

- Statsv endpoint
- Setup awesome hosts file additions & removals

