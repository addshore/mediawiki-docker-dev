<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\DockerCompose;
use M1\Env\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Destroy extends Command
{

	protected static $defaultName = 'v0:destroy';

	protected function configure()
	{
		$this->setDescription('Shut down the containers, and destroy them. Also deletes databases and volumes.');
		$this->setHelp('Shut down the containers, and destroy them. Also deletes databases and volumes.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		echo "Containers and volumes are being destroyed\n";
		(new DockerCompose())->downWithVolumes();

		if(file_exists(MWDD_DIR . '/.hosts')) {
			unlink( file_exists(MWDD_DIR . '/.hosts') );
		}
		return 0;
	}
}
