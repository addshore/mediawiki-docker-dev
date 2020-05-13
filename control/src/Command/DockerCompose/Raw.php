<?php

namespace Addshore\Mwdd\Command\DockerCompose;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Raw extends Command
{

	protected static $defaultName = 'dc:raw';

	protected function configure()
	{
		$this->setDescription('Runs a command in the docker-compose context.');
		$this->setHelp( <<< EOT
Examples:

View the last 10 logs of the db-configure service:
    dc:raw -- logs --tail=10 db-configure
EOT
		);
		$this->addArgument('args', InputArgument::IS_ARRAY );

	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$args = $input->getArgument('args');

		(new DockerCompose())->raw( implode( ' ', $args ) );
		return 0;
	}
}
