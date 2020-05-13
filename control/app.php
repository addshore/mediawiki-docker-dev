#!/usr/bin/env php
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

define('MWDD_DIR', dirname( __DIR__ ));

if ( $_SERVER['PHP_SELF'] !== 'control/app.php' ) {
    echo "You are running the control app from the wrong context.\n";
    echo "Please run the app from the root mediawiki-docker-dev directory.\n";
    die();
}

$application = new \Symfony\Component\Console\Application('mwdd');

$application->add(new \Addshore\Mwdd\Command\Base\Create());
$application->add(new \Addshore\Mwdd\Command\Base\Suspend());
$application->add(new \Addshore\Mwdd\Command\Base\Resume());

$application->add(new \Addshore\Mwdd\Command\DockerCompose\Raw());
$application->add(new \Addshore\Mwdd\Command\DockerCompose\Ps());

$application->add(new \Addshore\Mwdd\Command\Control\Up());
$application->add(new \Addshore\Mwdd\Command\Control\Stop());
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
