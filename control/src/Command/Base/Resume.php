<?php

namespace Addshore\Mwdd\Command\Base;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Resume extends Command
{

	protected static $defaultName = 'base:resume';

	protected function configure()
	{
		$this->setDescription('Restarts already setup base systems.');
		$this->setHelp('Restarts already setup base systems.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->start( Base::SERVICES);
		return 0;
	}
}
