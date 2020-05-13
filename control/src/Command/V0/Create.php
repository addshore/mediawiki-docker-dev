<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\DockerCompose;
use M1\Env\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	protected static $defaultName = 'v0:create';

	protected function configure()
	{
		$this->setDescription('Create and start the container, installing a default site.');
		$this->setHelp('Create and start the container, installing a default site.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# Create a combined .env file
		$defaultEnv = (new Parser(file_get_contents(MWDD_DIR . '/default.env')));
		$localEnv = (new Parser(file_get_contents(MWDD_DIR . '/local.env')));
		$combinesLines = array_merge(
			$defaultEnv->lines,
			$localEnv->lines
		);
		$finalLines = '';
		foreach( $combinesLines as $key => $line ) {
			$finalLines .= $key . '=' . "${line}" . PHP_EOL;
		}
		file_put_contents( MWDD_DIR . '/.env', $finalLines );

		# Start containers
		echo "Containers starting\n";
		(new DockerCompose())->upDetached();

		# Change owners
		(new DockerCompose())->exec( 'web', 'chown application:application //var/www/mediawiki' );
		(new DockerCompose())->exec( 'web', 'application:application //var/www/mediawiki/LocalSettings.php' );

		# Add document root index file (NOTE: docker-compose lacks a "cp" command)
		(new Docker())->cp(
			'config/mediawiki/index.php',
			(new DockerCompose())->psQ('web') . '://var/www/index.php'
		);
		(new DockerCompose())->exec( 'web', 'chown application:application //var/www/index.php' );

		# Wait for the db servers
		echo "Waiting for the db servers\n";
		echo "Sometimes this can take some time...\n";
		(new DockerCompose())->exec( 'web', '//srv/wait-for-it.sh db-master:3306' );
		(new DockerCompose())->exec( 'web', '//srv/wait-for-it.sh db-slave:3306' );

		# Reset local hosts file
		if(file_exists(MWDD_DIR . '/.hosts')) {
			unlink( file_exists(MWDD_DIR . '/.hosts') );
		}

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'proxy.mw.localhost' ]), $output );
		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'phpmyadmin.mw.localhost' ]), $output );
		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'graphite.mw.localhost' ]), $output );

		echo "Setting up log directory\n";
		(new DockerCompose())->exec( 'web', 'mkdir -p //var/log/mediawiki' );
		(new DockerCompose())->exec( 'web', 'chown application:application //var/log/mediawiki' );

		echo "Setting up images directory\n";
		(new DockerCompose())->exec( 'web', 'chown application:application //var/www/mediawiki/images/docker' );

		$this->getApplication()->find('v0:add-site')->run( new ArrayInput([ 'default' ]), $output );

		echo "Your development environment is running\n";
		return 0;

	}
}
