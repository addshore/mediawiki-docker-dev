<?php

namespace Addshore\Mwdd\Command\Base;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Files\MediawikiDir;
use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\DockerCompose;
use Addshore\Mwdd\Shell\Id;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	protected static $defaultName = 'base:create';

	protected function configure()
	{
		$this->setDescription('Create a the most basic development environment.');
		$serviceString = implode( ', ', Base::SERVICES );
		$this->setHelp(<<< EOT
Creates the most basic development environment, with the following services:
${serviceString}
EOT
);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DotEnv())->updateFromDefaultAndLocal();

		# Start containers
		$output->writeln('Starting services: ' . implode( ',', Base::SERVICES ));
		(new DockerCompose())->upDetached( Base::SERVICES );

		# Add document root index file (NOTE: docker-compose lacks a "cp" command)
		# TODO why is this not just in the docker-compose yml?
		(new Docker())->cp(
			'config/mediawiki/index.php',
			(new DockerCompose())->psQ(Base::SRV_MEDIAWIKI) . '://var/www/index.php'
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
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, '//srv/wait-for-it.sh db-master:3306' );

		# Reset local hosts file
		if(file_exists(MWDD_DIR . '/.hosts')) {
			unlink( MWDD_DIR . '/.hosts' );
		}

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => 'proxy.mw.localhost' ]), $output );

		$this->getApplication()->find('v0:addsite')->run( new ArrayInput([ 'site' => 'default' ]), $output );

		$output->writeln('Your development environment is running');

		return 0;

	}
}
