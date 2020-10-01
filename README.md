# Instructions

## Installation

By default the below steps will install MediaWiki at `~/src/mediawiki`
and start a server for <http://default.web.mw.localhost:8080>.

Many aspect of the container, including the port and MediaWiki path, can be customised
by creating a `local.env` in the root directory of this project, in which to override one or more variables
from `default.env`.

### Semi-automatic Installation

There is a setup script that you can run with `INSTALL_DIR=~/src ./setup.sh` if you already have Docker
installed and want to skip the manual steps 2-4. Note that `INSTALL_DIR` is the parent directory where MediaWiki
core will be downloaded, so in the example above, you would end up with a codebase at `~/src/mediawiki`.

After using the `./setup.sh` script, continue from [step 5](#5-create-a-basic-localsettingsphp).

### Manual Installation

#### 1) Install Docker & Docker Compose

[Docker Compose Installation](https://docs.docker.com/compose/install/)

##### Unix notes

- Use the `docker-ce` package, not the `docker` package (read [Docker Inc.'s installation instructions](https://docs.docker.com/install/))
- If you want to avoid logging in as root or sudo commands, you will have to add your user to the docker group:
See [How can I use Docker without sudo](https://askubuntu.com/questions/477551/how-can-i-use-docker-without-sudo#477554)
  - This does not mean your containers will not run as root. These are different settings not really used currently by this dev setup.

#### 2) Clone this repository

```bash
git clone https://github.com/addshore/mediawiki-docker-dev.git
```

#### 3) Clone MediaWiki core & the Vector skin

You can start without the skin but you will find that your MediaWiki install doesn't look very nice.

From [Wikimedia Gerrit](https://gerrit.wikimedia.org/r/#/admin/projects/mediawiki/core):

```bash
git clone https://gerrit.wikimedia.org/r/mediawiki/core /srv/dev/git/gerrit/mediawiki
git clone https://gerrit.wikimedia.org/r/mediawiki/skins/Vector /srv/dev/git/gerrit/mediawiki/skins/Vector
```

(You can clone your code to somewhere other than `/srv/dev/git/gerrit/mediawiki`. For example, `~/src/mediawiki` but you'll need to follow step 6 carefully.)

#### 4) Run `composer install` for MediaWiki

Either on your host machine inside the `/srv/dev/git/gerrit/mediawiki` directory (you need [composer locally](https://getcomposer.org/download/)):

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

#### 5) Create a basic LocalSettings.php

A `.docker/LocalSettings.php` file exists within the containers running Mediawiki. Your `LocalSettings.php` file must load it.

Make a `LocalSettings.php` in the root of the MediaWiki repo containing the following:

```php
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
```

When you come to change any MediaWiki settings this is the file you will want to be altering.

For example after install you will probably find you want to load the default skin:

```php
wfLoadSkin( 'Vector' );
```

#### 6) Configure the environment

Note: If you cloned mediawiki into a directory other than `/srv/dev/git/gerrit/mediawiki` you will need to do this step, otherwise the defaults can likely be used.

Copy the content of `default.env` from the `mediawiki-docker-dev` dir into a new file called `local.env`.

Alter any settings that you want to change, for example the install location of MediaWiki, a directory to a local composer cache, or webserver or php version.

Under section `# Location of the MediaWiki repo on your machine` set the **full** path to the mediawiki you cloned from gerrit.
Under section `# PHP Runtime version` set the php version that your mediawiki needs to work with. Setting `RUNTIMEVERSION` to `'latest'` doesn't always work. You might want to specify the php version exactly, e.g. `RUNTIMEVERSION=7.3`.

#### 7) Create the environment

**Create and start the Docker containers:**

```bash
./create
```

**Note:**
The script waits up to 120 secs (4 x 30 seconds) for the database containers to initialize and respond. In case the deployment takes longer than that on your system, please increase the default timeout value (line 139 in `scripts/wait-for-it.sh`).

```bash
TIMEOUT=${TIMEOUT:-30}
```

#### 8) Configure name resolution

For host name resolution of the containers inside the docker network, this setup includes [DPS](http://mageddo.github.io/dns-proxy-server/latest/en/), a DNS Proxy server. All wikis at `*.web.mw.localhost` will automatically resolve to the nginx-proxy container's IP address. The web container's name is `mediawiki.mw.localhost` and all other containers can be reached from each other via their corresponding host names, defined in `docker-compose.yml`:
 - `dps.mw.localhost`
 - `db-master.mw.localhost`
 - `db-slave.mw.localhost`
 - `phpmyadmin.mw.localhost`
 - `graphite.mw.localhost`
 - `proxy.mw.localhost`
 - `redis.mw.localhost`

In order to access the containers by name from your docker host system, there are two different ways:

##### Option a) Via /etc/hosts

Add the following to your `/etc/hosts` file:
```text
127.0.0.1 default.web.mw.localhost # mediawiki-docker-dev
127.0.0.1 proxy.mw.localhost # mediawiki-docker-dev
127.0.0.1 phpmyadmin.mw.localhost # mediawiki-docker-dev
127.0.0.1 graphite.mw.localhost # mediawiki-docker-dev
```
You can use the `./hosts-sync` script to try and update it automatically if possible. You may need to use `sudo ./hosts-sync` instead, if the file is not writable by the shell user.

##### Option b) Via DPS

Alternatively, you can use DPS to resolve the container names from your host system. By default, this feature is disabled. To enable it, add a `dps` section to your `docker-compose.override.yml` file or create it as follows:
```text
version: '2.2'

services:
  dps:
    volumes:
      - /etc/resolv.conf:/etc/resolv.conf
```
This will allow DPS to modify your `/etc/resolv.conf` and set itself as your system's primary DNS server. No changes to the `/etc/hosts` file are needed in that case.

## Commands

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

### Maintenance Scripts

```
mw-docker-dev script default maintenance/update.php
```

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

To do so you need to set `IDELOCALHOST` in you local.env file to the IP of your local machine (where you run your IDE) as it appears to docker containers.

To determine the IP of your local machine as it appears to the docker container you can try the following:
* See the default route of the container:
  ** `./bash` and run `route` and use the "Gateway" for the "default" "Destination"
  ** Use the ip assigned to your developer machine by your router (e.g. type `ip addr`)

xdebug connections will then be sent to this IP address on port 9000.

Verify in php.ini that `xdebug_remote_host` is set to the value of `IDELOCALHOST` and `xdebug_remote_port` is 9000. (You may need to destroy and create your containers)

**VS Code Configuration**
If using Visual Studio Code editor, add the following to your `launch.json`.

```json
"pathMappings": {
    "/var/www/mediawiki": "${workspaceFolder}"
}
```

**CLI**

For debugging CLI commands from the container try running the following before you run your script:
```
export XDEBUG_CONFIG="remote_host=${XDEBUG_REMOTE_HOST}"
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

## Adding Wikidata Query Service

Mediawiki-docker-dev can be extended with the wikidata-query service by following these steps.

- Add the following example yaml markup to your `docker-compose.override.yml` file
- Add a `.htaccess` file in the root of this repository
- Create a symlink from `/var/www/mediawiki` to `/var/www/w` in your web container so that **wdqs-updater** can reach your local wikibase through `/w/api.php`. Example: `docker exec -it mediawiki-docker-dev_web_1 ln -sn /var/www/mediawiki /var/www/w`
- Make sure the following exists in your LocalSettings.php. `$wgScriptPath = "/w";` and `$wgArticlePath = "/wiki/$1";`

Example `docker-compose.override.yml`
```yaml
version: '2.2'

services:
  wdqs-updater:
    image: wikibase/wdqs:latest
    restart: unless-stopped
    command: /runUpdate.sh
    depends_on:
    - wdqs
    - web
    networks:
     - dps
    environment:
     - WIKIBASE_HOST=default.web.mw.localhost:8080
     - WDQS_HOST=wdqs
     - WDQS_PORT=9999
     - WDQS_ENTITY_NAMESPACES=120,122
    dns:
     - 172.0.0.10
  wdqs:
    image: wikibase/wdqs:latest
    volumes:
      - query-service-data:/wdqs/data
    environment:
     - WIKIBASE_HOST=default.web.mw.localhost:8080
     - WDQS_HOST=wdqs.mw.localhost
     - WDQS_PORT=9999
     - WDQS_ENTITY_NAMESPACES=120,122
     - WIKIBASE_SCHEME=http
    dns:
      - 172.0.0.10
    networks:
      - dps
    restart: unless-stopped
    command: /runBlazegraph.sh
    hostname: wdqs.mw.localhost
    expose:
        - 9999
  wdqs-proxy:
    image: wikibase/wdqs-proxy
    restart: unless-stopped
    environment:
      - PROXY_PASS_HOST=wdqs:9999
    ports:
     - "8989:80"
    depends_on:
    - wdqs
    networks:
      - dps
  web:
    volumes:
     - .htaccess:/var/www/.htaccess

volumes:
  query-service-data:

```
The `.htaccess` file is needed to do rewrites of the requests issued by wdqs-updater. Place the file in the mediawiki-docker-dev source folder and it will be mounted into the web container.
```
RewriteEngine On

RewriteRule ^/?wiki(/.*)?$ %{DOCUMENT_ROOT}/mediawiki/index.php [L]

# rewrite /entity/ URLs like wikidata per
# https://meta.wikimedia.org/wiki/Wikidata/Notes/URI_scheme
RewriteRule ^/?entity/(.*)$ /wiki/Special:EntityData/$1 [R=303,QSA]

```

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

### host.docker.internal

This no longer seemed to work since a custom internal DNS service is used.

In order to easily find out the IP of your host machine try:
```
 docker run --rm -it busybox nslookup host.docker.internal
```

### Windows

**/usr/bin/env: ‘bash\r’: No such file or directory**

https://stackoverflow.com/questions/29045140/env-bash-r-no-such-file-or-directory

## TODO

- Statsv endpoint
- Setup awesome hosts file additions & removals

