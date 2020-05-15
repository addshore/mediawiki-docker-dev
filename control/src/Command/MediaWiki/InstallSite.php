<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallSite extends Command
{

	protected static $defaultName = 'mw:installsite';

	protected function configure()
	{
		$this->setHidden(true);
		$this->setDescription('Installs a new MediaWiki site (on the running base setup).');
		$this->addArgument( 'site' );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$site = $input->getArgument( 'site' );
		$output->writeln("Adding new site: " . $site);

		// Make some directories that ONLY exist within the container (anon volume)
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'mkdir -m 777 -p //var/www/mediawiki/images/docker/' . $site );
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'mkdir -m 777 -p //var/www/mediawiki/images/docker/' . $site . '/tmp' );
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'mkdir -m 777 -p //var/www/mediawiki/images/docker/' . $site . '/cache' );

		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, 'bash //var/www/mediawiki/.docker/installdbs ' . $site );

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => $site . '.web.mw.localhost' ]), $output );
		return 0;
	}

}
