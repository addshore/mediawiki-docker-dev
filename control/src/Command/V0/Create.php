<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Files\MediawikiDir;
use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\DockerCompose;
use Addshore\Mwdd\Shell\Id;
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
		$this->setHidden(true);
		$this->setDescription('Create and start the containers, installing a default site.');
		$this->setHelp('Create and start the containers, installing a default site.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DotEnv())->updateFromDefaultAndLocal();

		# Start containers
		$output->writeln('Containers starting');
		(new DockerCompose())->upDetached( Legacy::SERVICES );

		# Add document root index file (NOTE: docker-compose lacks a "cp" command)
		# TODO why is this not just in the docker-compose yml?
		(new Docker())->cp(
			'config/mediawiki/index.php',
			(new DockerCompose())->psQ(Legacy::SRV_MEDIAWIKI) . '://var/www/index.php'
		);

		# Chown some things...
		# TODO should this be in the entrypoint? YES!
		(new DockerCompose())->exec(
			Legacy::SRV_MEDIAWIKI,
			'chown ' . (new Id())->ug() . ' //var/log/mediawiki //var/www/mediawiki/images/docker',
			'--user root'
		);

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


		$this->getApplication()->find('v0:addsite')->run( new ArrayInput([ 'site' => 'default' ]), $output );

		$output->writeln('Your development environment is running');

		return 0;

	}
}
