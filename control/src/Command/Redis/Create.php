<?php

namespace Addshore\Mwdd\Command\Redis;

use Addshore\Mwdd\DockerCompose\Redis;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	protected static $defaultName = 'redis:create';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DotEnv())->updateFromDefaultAndLocal();

		$output->writeln('Starting services: ' . implode( ',', Redis::SERVICES ));
		(new DockerCompose())->upDetached( Redis::SERVICES );

		return 0;

	}
}
