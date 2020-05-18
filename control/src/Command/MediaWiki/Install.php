<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\Command\TraitForCommandsThatAddHosts;
use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\Shell\DockerCompose;
use Addshore\Mwdd\Shell\Id;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{

	use TraitForCommandsThatAddHosts;

	protected static $defaultName = 'mw:install';

	protected function configure()
	{
		$this->setDescription('Installs a new MediaWiki site.');
		$this->setHelp(<<<EOH
This command does the following:

1) Creates some directories in the container for images, tmp storage and caching (777 permissions).
   - /app/images/docker/<sitename>
   - /app/images/docker/<sitename>/tmp
   - /app/images/docker/<sitename>/cache

2) Runs the installdbs shell script which:
   - Waits for db-master service to be ready
   - Moves the user LocalSettings.php file out of the way
   - Runs install.php
   - Moves the user LocalSettings.php back
   - Runs update.php
EOH
);

		$this->addArgument( 'site', InputArgument::OPTIONAL, 'The site name to install', 'default' );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$site = $input->getArgument( 'site' );
		$output->writeln("Adding new site: " . $site);

		// Make some directories that ONLY exist within the container (anon volume)
		// TODO could chown the dirs to someone else?
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'mkdir -m 777 -p //app/images/docker/' . $site );
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'mkdir -m 777 -p //app/images/docker/' . $site . '/tmp' );
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'mkdir -m 777 -p //app/images/docker/' . $site . '/cache' );

		$ug = (new Id())->ug();
		// TODO try to output these commands as they are running to the user for observability..?
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'bash //mwdd-custom/installdbs ' . $site, "--user ${ug}" );

		$this->addHostsAndPrintOutput( [ $site . '.web.mw.localhost' ], $output );
		return 0;
	}

}
