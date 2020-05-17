#!/usr/bin/env php
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ( $_SERVER['PHP_SELF'] !== 'control/app.php' ) {
	echo "You are running the control app from the wrong context.\n";
	echo "Please run the app from the root mediawiki-docker-dev directory.\n";
	die();
}

if(file_exists(__DIR__.'/vendor/autoload.php')){
	// Load a local vendor dir if it exits
	require_once __DIR__.'/vendor/autoload.php';
} elseif( file_exists('/mwdd-vendor') ) {
	// If we are running in the control container, we can just copy the vendor that we made when building the image.
	shell_exec('cp -R /mwdd-vendor ' . __DIR__ . '/vendor'); // XXX FIXME: this will copy as root?
	// Try again with our copied vendor dir from the control image
	require_once __DIR__.'/vendor/autoload.php';
} else {
	echo "You either need to:\n";
	echo " - Use a php environment locally and have done a composer install of the control directory.";
	echo " - Use a docker-compose environment which will populate the control vendor directory for you. (as root FIXME)";
	die();
}

define('MWDD_DIR', dirname( __DIR__ ));

$application = new \Symfony\Component\Console\Application('mwdd');

$application->add(new \Addshore\Mwdd\Command\Base\HostsAdd());

// TODO register these using a factory or something...
$mwHelp = <<<EOH
MediaWiki.

MediaWiki will be accessible at a location such as: http://default.web.mw.localhost:8080/api.php
EOH;
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('mw', $mwHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('mw', $mwHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('mw', $mwHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('mw', $mwHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Logs('mw', $mwHelp));
$application->add(new \Addshore\Mwdd\Command\MediaWiki\GetCode());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\PHPUnit());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Composer());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Fresh());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Maint());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Quibble());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Install());

$adminerHelp = 'Adminer is a tool for managing content in MySQL databases.';
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('adminer', $adminerHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('adminer', $adminerHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('adminer', $adminerHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('adminer', $adminerHelp));

$phpMyAdminHelp = 'phpMyAdmin is a free and open source administration tool for MySQL and MariaDB.';
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('phpmyadmin', $phpMyAdminHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('phpmyadmin', $phpMyAdminHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('phpmyadmin', $phpMyAdminHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('phpmyadmin', $phpMyAdminHelp));

$redisHelp = 'Redis is an open source (BSD licensed), in-memory data structure store, used as a database, cache and message broker.';
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('redis', $redisHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('redis', $redisHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('redis', $redisHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('redis', $redisHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Cli('redis', 'redis-cli', $redisHelp));

$statsdHelp = 'Statsd and Graphite allow for simple time series data collection.';
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('statsd', $statsdHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('statsd', $statsdHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('statsd', $statsdHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('statsd', $statsdHelp));

$masterHelp = <<<EOH
A primary MySql server (master).

You can alter the image that is used in you local.env file.
EOH;
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('db', $masterHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('db', $masterHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('db', $masterHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('db', $masterHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Cli('db', 'mysql', $masterHelp));

$replicaHelp = <<< EOH
A second MySql server with automatic replication from the master.

You can alter the image that is used in you local.env file.

Upon startup it might take a short while for server to catch up with the master, depending on how much data has been written.

You can check the replication status using <info>SHOW SLAVE STATUS</info>
EOH;
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Create('db-replica', $replicaHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Suspend('db-replica', $replicaHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Resume('db-replica', $replicaHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Exec('db-replica', $replicaHelp));
$application->add(new \Addshore\Mwdd\Command\ServiceBase\Cli('db-replica', 'mysql', $replicaHelp));

$application->add(new \Addshore\Mwdd\Command\DockerCompose\Raw());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Ps());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Logs());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Destroy());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Bash());

$application->add(new \Addshore\Mwdd\Command\Control\Create());
$application->add(new \Addshore\Mwdd\Command\Control\Suspend());
$application->add(new \Addshore\Mwdd\Command\Control\Bash());

$application->run();
