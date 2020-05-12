<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MySql extends Command
{

	protected static $defaultName = 'v0:mysql';

	protected function configure()
	{
		$this->setDescription('Runs mysql cli in the specified mysql service.');
		$this->setHelp('Runs mysql cli in the specified mysql service.');
		$this->addArgument('host');
		// The origional v0 bash implementation allowed passing through extra params, but it is
		// unclear if that is needed, so don't bother right now...
		//$this->ignoreValidationErrors();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$host = $input->getArgument('host');
		// TODO grab user and password from docker-compose or env vars?
		(new Commands())->exec( $host, 'mysql --user=root --password=toor' );
	}
}
