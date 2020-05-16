<?php

namespace Addshore\Mwdd\Command\Adminer;

use Addshore\Mwdd\DockerCompose\Adminer;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Suspend extends Command
{

	protected static $defaultName = 'adminer:suspend';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->stop(Adminer::SERVICES);
		return 0;
	}
}
