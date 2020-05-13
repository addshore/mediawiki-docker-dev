<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Legacy;
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
		$this->setDescription('Create and start the containers, installing a default site.');
		$this->setHelp('Create and start the containers, installing a default site.');
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
		$output->writeln('Containers starting');
		(new DockerCompose())->upDetached( Legacy::SERVICES );

		# Change owners
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'chown application:application //var/www/mediawiki' );
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'chown application:application //var/www/mediawiki/LocalSettings.php' );

		# Add document root index file (NOTE: docker-compose lacks a "cp" command)
		(new Docker())->cp(
			'config/mediawiki/index.php',
			(new DockerCompose())->psQ(Legacy::SRV_MEDIAWIKI) . '://var/www/index.php'
		);
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'chown application:application //var/www/index.php' );

		# Wait for the db servers
		$output->writeln('Waiting for the db servers');
		$output->writeln('Sometimes this can take some time...');
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, '//srv/wait-for-it.sh db-master:3306' );
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, '//srv/wait-for-it.sh db-replica:3306' );

		# Reset local hosts file
		if(file_exists(MWDD_DIR . '/.hosts')) {
			unlink( MWDD_DIR . '/.hosts' );
		}

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => 'proxy.mw.localhost' ]), $output );
		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => 'phpmyadmin.mw.localhost' ]), $output );
		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => 'graphite.mw.localhost' ]), $output );

		$output->writeln('Setting up log directory');
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'mkdir -p //var/log/mediawiki' );
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'chown application:application //var/log/mediawiki' );

		$output->writeln('Setting up images directory');
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'chown application:application //var/www/mediawiki/images/docker' );

		$this->getApplication()->find('v0:addsite')->run( new ArrayInput([ 'site' => 'default' ]), $output );

		$output->writeln('Your development environment is running');

		return 0;

	}
}
