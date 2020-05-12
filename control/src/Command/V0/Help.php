<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Help extends Command
{

	protected static $defaultName = 'v0:bash';

	protected function configure()
	{
		$this->setDescription('Outputs help for V0 commands.');
		$this->setHelp('Outputs help for V0 commands.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		echo "Commands are: v0:create, v0:destroy, v0:resume, v0:suspend, v0:addsite, v0:mysql, v0:phpunit, v0:phpunit-file";
	}
}
