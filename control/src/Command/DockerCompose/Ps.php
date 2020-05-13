<?php

namespace Addshore\Mwdd\Command\DockerCompose;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Ps extends Command
{

	protected static $defaultName = 'dc:ps';

	protected function configure()
	{
		$this->setDescription('Runs docker-compose ps in the correct context.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->ps();
		return 0;
	}
}
