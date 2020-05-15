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

$application->add(new \Addshore\Mwdd\Command\Base\Create());
$application->add(new \Addshore\Mwdd\Command\Base\Suspend());
$application->add(new \Addshore\Mwdd\Command\Base\Resume());

$application->add(new \Addshore\Mwdd\Command\MediaWiki\GetCode());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\PHPUnit());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Composer());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Fresh());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\LogsTail());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Bash());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Maint());
$application->add(new \Addshore\Mwdd\Command\MediaWiki\Quibble());

$application->add(new \Addshore\Mwdd\Command\DbReplica\Create());
$application->add(new \Addshore\Mwdd\Command\DbReplica\Suspend());
$application->add(new \Addshore\Mwdd\Command\DbReplica\Resume());
$application->add(new \Addshore\Mwdd\Command\DbReplica\MySql());

$application->add(new \Addshore\Mwdd\Command\PhpMyAdmin\Create());
$application->add(new \Addshore\Mwdd\Command\PhpMyAdmin\Suspend());
$application->add(new \Addshore\Mwdd\Command\PhpMyAdmin\Resume());

$application->add(new \Addshore\Mwdd\Command\Statsd\Create());
$application->add(new \Addshore\Mwdd\Command\Statsd\Suspend());
$application->add(new \Addshore\Mwdd\Command\Statsd\Resume());

$application->add(new \Addshore\Mwdd\Command\Redis\Create());
$application->add(new \Addshore\Mwdd\Command\Redis\Suspend());
$application->add(new \Addshore\Mwdd\Command\Redis\Resume());
$application->add(new \Addshore\Mwdd\Command\Redis\Cli());

$application->add(new \Addshore\Mwdd\Command\DockerCompose\Raw());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Ps());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Logs());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Destroy());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Bash());

$application->add(new \Addshore\Mwdd\Command\Control\Create());
$application->add(new \Addshore\Mwdd\Command\Control\Suspend());
$application->add(new \Addshore\Mwdd\Command\Control\Bash());

$application->add(new \Addshore\Mwdd\Command\V0\Suspend());
$application->add(new \Addshore\Mwdd\Command\V0\Resume());
$application->add(new \Addshore\Mwdd\Command\V0\Bash());
$application->add(new \Addshore\Mwdd\Command\V0\MySql());
$application->add(new \Addshore\Mwdd\Command\V0\PHPUnit());
$application->add(new \Addshore\Mwdd\Command\V0\PHPUnitFile());
$application->add(new \Addshore\Mwdd\Command\V0\Script());
$application->add(new \Addshore\Mwdd\Command\V0\LogsTail());
$application->add(new \Addshore\Mwdd\Command\V0\HostsAdd());
$application->add(new \Addshore\Mwdd\Command\V0\Create());
$application->add(new \Addshore\Mwdd\Command\V0\Destroy());
$application->add(new \Addshore\Mwdd\Command\V0\AddSite());
$application->add(new \Addshore\Mwdd\Command\V0\HostsAdd());

$application->run();
