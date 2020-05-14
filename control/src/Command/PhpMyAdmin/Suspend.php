<?php

namespace Addshore\Mwdd\Command\PhpMyAdmin;

use Addshore\Mwdd\DockerCompose\PhpMyAdmin;
use Addshore\Mwdd\DockerCompose\Redis;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Suspend extends Command
{

	protected static $defaultName = 'phpmyadmin:suspend';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->stop(PhpMyAdmin::SERVICES);
		return 0;
	}
}
