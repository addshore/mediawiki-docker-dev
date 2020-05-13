<?php

namespace Addshore\Mwdd\Command\Control;

use Addshore\Mwdd\DockerCompose\Control;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	protected static $defaultName = 'ctrl:create';

	protected function configure()
	{
		$this->setDescription('Creates the control container (building if needed).');
		$this->setHidden(true);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->upDetached( Control::SERVICES, $input->getOption('build'));
		return 0;
	}
}
