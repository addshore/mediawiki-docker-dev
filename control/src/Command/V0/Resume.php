<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Resume extends Command
{

	protected static $defaultName = 'v0:resume';

	protected function configure()
	{
		$this->setDescription('Restarts an already setup development system.');
		$this->setHelp('Restarts an already setup development system.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new Commands())->start();
	}
}
