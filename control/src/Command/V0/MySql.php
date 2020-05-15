<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MySql extends Command
{

	protected static $defaultName = 'v0:mysql';

	protected function configure()
	{
		$this->setHidden(true);
		$this->setDescription('Runs mysql cli in the specified mysql service.');
		$this->setHelp('Runs mysql cli in the specified mysql service.');
		$this->addArgument('host');
		// The origional v0 bash implementation allowed passing through extra params, but it is
		// unclear if that is needed, so don't bother right now...
		//$this->ignoreValidationErrors();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Backcompat as the host name changed...
		$host = $input->getArgument('host');
		if($host === 'db-slave') {
			$host = 'db-replica';
		}

		// TODO grab user and password from docker-compose or env vars?
		(new DockerCompose())->exec( $host, 'mysql --user=root --password=toor' );
		return 0;
	}
}
