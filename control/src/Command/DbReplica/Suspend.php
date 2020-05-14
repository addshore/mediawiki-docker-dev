<?php

namespace Addshore\Mwdd\Command\DbReplica;

use Addshore\Mwdd\DockerCompose\DbReplica;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Suspend extends Command
{

	protected static $defaultName = 'db:replica:suspend';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->stop(DbReplica::SERVICES);
		return 0;
	}
}
