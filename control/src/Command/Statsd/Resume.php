<?php

namespace Addshore\Mwdd\Command\Statsd;

use Addshore\Mwdd\DockerCompose\Redis;
use Addshore\Mwdd\DockerCompose\Statsd;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Resume extends Command
{

	protected static $defaultName = 'statsd:resume';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->start( Statsd::SERVICES);
		return 0;
	}
}
