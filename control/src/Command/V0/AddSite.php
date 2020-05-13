<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\DockerCompose;
use M1\Env\Parser;
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
		echo "Adding new site: " . $site . PHP_EOL;

		(new DockerCompose())->exec( 'web', 'mkdir -p //var/www/mediawiki/images/docker/' . $site, '--user application' );
		(new DockerCompose())->exec( 'web', 'mkdir -p //var/www/mediawiki/images/docker/' . $site . '/tmp', '--user application' );
		(new DockerCompose())->exec( 'web', 'mkdir -p //var/www/mediawiki/images/docker/' . $site . '/cache', '--user application' );

		(new DockerCompose())->exec( 'web', 'bash //var/www/mediawiki/.docker/installdbs ' . $site, '--user application' );

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ $site . '.web.mw.localhost' ]), $output );
		return 0;
	}

}
