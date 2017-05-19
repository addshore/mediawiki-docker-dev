## Instructions

If you don't want to use the default port of 8080 and the default mediawiki path of ~/dev/git/gerrit/mediawiki then please just change the .env file for now....

All scripts (install, uninstall, up, down) will need sudo on linux systems for docker to work.

### Install

#### 1) Install Docker & Docker Compose

https://docs.docker.com/compose/install/

#### 2) Clone this repository

```
git clone https://github.com/addshore/mediawiki-docker-dev.git ~/dev/github/addshore/mediawiki-docker-dev
```

#### 3) Clone MediaWiki Core

From [Wikimedia Gerrit](https://gerrit.wikimedia.org/r/#/admin/projects/mediawiki/core):

```
git clone https://gerrit.wikimedia.org/r/mediawiki/core ~/dev/gerrit/mediawiki
```

Or from [Github Mirror](https://github.com/wikimedia/mediawiki):

```
git clone https://github.com/wikimedia/mediawiki.git ~/dev/gerrit/mediawiki
git remote set-url origin https://gerrit.wikimedia.org/r/mediawiki/core
```

#### 4) Create a basic LocalSettings.php

The .docker/LocalSettings.php file will exist within the containers running Mediawiki.

Make a LocalSettings.php in the root of the Mediawiki repo containing the following:

```
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
```

#### 5) Install Mediawiki databases

```
~/dev/github/addshore/mediawiki-docker-dev/up
~/dev/github/addshore/mediawiki-docker-dev/install
~/dev/github/addshore/mediawiki-docker-dev/down
```

#### 6) Update your local hosts file

You will need to check the ./config/local/hosts file and add these to your local hosts file.

### Operation

You need to populate your hosts file to get the most out of this docker stuff.

Running the up script will tell you what you need to add.

**To start the containers**:

```
~/dev/github/addshore/mediawiki-docker-dev/up
```

**To stop the containers**:

```
~/dev/github/addshore/mediawiki-docker-dev/down
```

### Access

#### Web

**Tools**

 - [Graphite](http://graphite.mw:8080)
 - [PhpMyAdmin](http://phpmyadmin.mw:8080)
 
**Mediawiki**

 - [Nginx & PHP5 & MySQL](http://nginx.php5.mysql.mw:8080)
 - [Nginx & PHP7 & MySQL](http://nginx.php7.mysql.mw:8080)
 - [Nginx & HHVM & MySQL](http://nginx.hhvm.mysql.mw:8080)
 - [Nginx & HHVM & MariaDb](http://nginx.hhvm.mariadb.mw:8080)
 - [Apache & HHVM & MariaDb](http://apache.hhvm.mariadb.mw:8080)
 - etc. http://<webserver>.<runtime>.<dbtype>.mw:8080

#### Container

You can run commands using the name of the container:
```
docker exec -it "mediawikidev_mediawiki-apache-php7_1" bash
```

If this doesn't work, Get the ID of the container that you want to run a command on:
```
docker container ls
```

And then use it to run a command:
```
docker exec -it "98ur10fkj1909j3" bash
```


### TODO

 - FIX HHVM strict mode
   - Strict Warning: It is not safe to rely on the system's timezone settings. Please use the date.timezone setting, the TZ environment variable or the date_default_timezone_set() function.
 - Statsv endpoint
 - Script to install mediawiki on one of the databases...
 - Setup awesomeness db names and stuff
   - [webserver].[runtime].[dbtype].[dbsuffix].mw <<< :( requires wildcard domains......
   - dbname = [dbtype].[dbsuffix]
