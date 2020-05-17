<?php

namespace Addshore\Mwdd\Command\DockerCompose;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Destroy extends Command
{

	protected static $defaultName = 'dc:destroy';

	protected function configure()
	{
		$this->setDescription('Shut down all containers, and destroy them. Also deletes databases and volumes.');
		$this->setHelp('Shut down all containers, and destroy them. Also deletes databases and volumes.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Containers and volumes are being destroyed.");
		(new DockerCompose())->downWithVolumesAndOrphans();

		// This is not related to the destroy functionality, but this is nice to clean this up here...
		if(file_exists(MWDD_DIR . '/.hosts')) {
			unlink( MWDD_DIR . '/.hosts' );
		}
		return 0;
	}
}
