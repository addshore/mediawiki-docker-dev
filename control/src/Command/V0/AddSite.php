<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddSite extends Command
{

	protected static $defaultName = 'v0:addsite';

	protected function configure()
	{
		$this->setDescription('Adds a new site to the setup.');
		$this->setHelp('Adds a new site to the setup.');
		$this->addArgument( 'site' );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$site = $input->getArgument( 'site' );
		$output->writeln("Adding new site: " . $site);

		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'mkdir -p //var/www/mediawiki/images/docker/' . $site, '--user application' );
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'mkdir -p //var/www/mediawiki/images/docker/' . $site . '/tmp', '--user application' );
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'mkdir -p //var/www/mediawiki/images/docker/' . $site . '/cache', '--user application' );

		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'bash //var/www/mediawiki/.docker/installdbs ' . $site, '--user application' );

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => $site . '.web.mw.localhost' ]), $output );
		return 0;
	}

}
