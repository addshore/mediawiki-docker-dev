<?php

namespace Addshore\Mwdd\Command\Control;

use Addshore\Mwdd\DockerCompose\Control;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stop extends Command
{

	protected static $defaultName = 'ctrl:stop';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->stop(Control::SERVICES);
		return 0;
	}
}
