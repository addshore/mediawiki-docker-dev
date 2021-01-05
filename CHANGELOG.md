## Changes

This file contains a brief overview of the Majorish changes to the environment.

Most recent changes are at the top.

### 2021

#### January 2021

* Allow encoded url slashes in mediawiki web server [#122](https://github.com/addshore/mediawiki-docker-dev/pull/122)
* Pull images as part of the `./create` script [#116](https://github.com/addshore/mediawiki-docker-dev/pull/116)

### 2020

#### March 2020

* DNS service has been added to the internal network (using defreitas/dns-proxy-server) [commit](https://github.com/addshore/mediawiki-docker-dev/commit/40dc10be5bdd4f716cab5dd8388da78faa29098b):
  * silvanwmde/nginx-proxy docker image will be used until [this PR](https://github.com/nginx-proxy/nginx-proxy/pull/1353) makes it into the "official" docker image
  * MediaWiki (and other services) can now resolve the *.mw.lcoalhost domains internally to the correct services

### 2019

#### December 2019

* Add "script" command for running maintenance scripts.

#### November 2019

* XDEBUG_REMOTE_AUTOSTART changes [commit](https://github.com/addshore/mediawiki-docker-dev/commit/135c51e5ceca511bfd0952723221ad3c8d596ceb)
  * Default values is now 0 (was 1)
  * XDEBUG_REMOTE_AUTOSTART can now be set from your local.env file
  * This results in speed ups when not debugging, as PHP doesnt try to connect back to the debugger on every request.
* Extend nginx proxy timeouts from 60s to 120s, as while debugging some web requests can take a long time. [commit](https://github.com/addshore/mediawiki-docker-dev/commit/a65c50589992934263df51aa65f6bf70d64d3ee4)

#### October 2019

* Default wait-for-it time set to 120 seconds
* installdbs now run as application user [commit](https://github.com/addshore/mediawiki-docker-dev/commit/ac153fe87c4c3e84eddb2d39558661e6ebe1d8fd)
* HHVM support removed (it never really worked anyway)
* Readme updates


#### September 2019

* Default config for wgCacheDirectory [commit](https://github.com/addshore/mediawiki-docker-dev/commit/90422f2a80c4bcf6bcf1d97c1d2f17d63961f5b2)
  * Also create said directory [commit](https://github.com/addshore/mediawiki-docker-dev/commit/f5a5c484ce3c7170c0080b9c6efe02a52e504c98)
* Default wait-for-it time set to 30 seconds [commit](https://github.com/addshore/mediawiki-docker-dev/commit/149dfe532abfa5b76214e35c6fc0b842fe93ba88)
* Ensure $HOME/.composer exists [commit](https://github.com/addshore/mediawiki-docker-dev/commit/3d5128657ffab3ab0136b382ac7784610a7dfe48)
* Readme updates


#### March 2019

###### 13th

* Added simple 'help' script / command to remind you of the other commands (useful if using a bash alias)

###### 1st

* Bumped default upload size for web host and proxy to 1024M

#### February 2019

###### 19th

* docker-compose.override.yml added to .gitignore

###### 6th

* [Command names changed](https://github.com/addshore/mediawiki-docker-dev/pull/81):
  * renamed  up     -> create
  * renamed  down   -> destroy
  * renamed  start  -> resume
  * renamed  stop   -> suspend

#### January 2019

###### 25th

* [Default admin password changed from "adminpass" to "dockerpass"](https://github.com/addshore/mediawiki-docker-dev/pull/79)

###### 23rd

* [default.env changes with extra documentation](https://github.com/addshore/mediawiki-docker-dev/pull/78)
* ['mysql' script introduced allowing easy access to the database on CLI](https://github.com/addshore/mediawiki-docker-dev/pull/77)

### 2018

* Changes after Jan 2018 were missed (sorry). Changelog resumed at Jan 2019...

#### January 2018

###### 16th

* Composer no longer runs as part of the `up` script

###### 10th

* Added `logs-tail` script to tail logs in the mediawiki log directory.
* Renamed `phpunit` script to `phpunit-file`.
* Added new `phpunit` which allows running phpunit for jobs etc. Example: `phpunit default --group=Database`
* `bash` script will now open in the `/var/www/mediawiki` directory
* PhpMyAdmin now shows a drop down allowing you to select the db servers rather than specifying their name.
* PhpMyAdmin now automatically shows blob data as text in results.
* Mediawiki setting default change: `$wgDevelopmentWarnings = true;`

### 2017

#### December 2017

###### 22nd

* [MediaWiki tmp directory now writable](https://github.com/addshore/mediawiki-docker-dev/issues/38)
* [Simplify phpunit script usage](https://github.com/addshore/mediawiki-docker-dev/issues/15)
Now you can simply do: `phpunit default tests/phpunit/includes/PageArchiveTest.php` for example.
* [Don't use replica db for phpunit tests](https://github.com/addshore/mediawiki-docker-dev/commit/cdfc830a75510b5250a4031ef104eec381ba969d)
Prior to this phpunit tests would fail due to temporary tables and the master & slave db setup.
* [$wgShowHostnames = true; by default](https://github.com/addshore/mediawiki-docker-dev/commit/e7f572dd339b41dbcdb316238a4b1d09f9935416)
To enable displaying db lags in the API by default.
* [DB Master & Slave setup](https://github.com/addshore/mediawiki-docker-dev/commit/60f8d68d9bcd7cf0e220aa123dda90825b43dc40)
Each wiki will now include a master and a slave.
* [Composer Cache mountable from local system](https://github.com/addshore/mediawiki-docker-dev/commit/60f8d68d9bcd7cf0e220aa123dda90825b43dc40)
You can now mount a local directory to use as the composer cache to speed up composer installs and updates.
See the `COMPOSER_CACHE_PATH_OR_VOL` env var.

#### November 2017

###### 30th

* [Use 'mediawiki' directory for MediaWiki](https://github.com/addshore/mediawiki-docker-dev/commit/2ba1eb6d093dd141f4f4321a3464af94fa4a6aa6)

###### 29th

* [Fix wgFavicon to use absolute path](https://github.com/addshore/mediawiki-docker-dev/commit/67d5d75507c979ac7a80a46a7951b40652d60bff)
* [Set MW_INSTALL_PATH in web containers](https://github.com/addshore/mediawiki-docker-dev/commit/28733515c9127401f010a6f331b30c3d678afd97)

Changes prior to late November 2017 do not have an entry here.
