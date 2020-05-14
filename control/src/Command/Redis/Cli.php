<?php

namespace Addshore\Mwdd\Command\Redis;

use Addshore\Mwdd\DockerCompose\DbReplica;
use Addshore\Mwdd\DockerCompose\Redis;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cli extends Command
{

	protected static $defaultName = 'redis:cli';

	protected function configure()
	{
		$this->setDescription('Runs redis cli against the redis service.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DockerCompose())->exec( Redis::SRV_REDIS, 'redis-cli' );
		return 0;
	}
}
