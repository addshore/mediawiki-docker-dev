<?php

namespace Addshore\Mwdd\Command\Redis;

use Addshore\Mwdd\DockerCompose\Redis;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Resume extends Command
{

	protected static $defaultName = 'redis:resume';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->start( Redis::SERVICES);
		return 0;
	}
}
