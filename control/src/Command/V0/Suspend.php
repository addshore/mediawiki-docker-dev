<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Suspend extends Command
{

	protected static $defaultName = 'v0:suspend';

	protected function configure()
	{
		$this->setDescription('Suspends an already running development system.');
		$this->setHelp('Suspends an already running development system.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->stop(Legacy::SERVICES);
		return 0;
	}
}
