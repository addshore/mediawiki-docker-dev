<?php

namespace Addshore\Mwdd\Command\Control;

use Addshore\Mwdd\DockerCompose\Control;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bash extends Command
{

	protected static $defaultName = 'ctrl:bash';

	protected function configure()
	{
		$this->setDescription('Runs bash in the Control container.');
		$this->setHelp('Runs bash in the Control container.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->exec( Control::SRV_CONTROL, 'bash' );
		return 0;
	}
}
