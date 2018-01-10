## Changes

This file contains a brief overview of the Majorish changes to the environment.

#### January 2018

* Added `logs-tail` script to tail logs in the mediawiki log directory.
* Renamed `phpunit` script to `phpunit-file`.
* Added new `phpunit` which allows running phpunit for jobs etc. Example: `phpunit default --group=Database`

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
