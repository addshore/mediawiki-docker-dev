<?php

namespace Addshore\Mwdd\Command\DbReplica;

use Addshore\Mwdd\DockerCompose\DbReplica;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MySql extends Command
{

	protected static $defaultName = 'db:replica:mysql';

	protected function configure()
	{
		$this->setDescription('Runs mysql cli against the db replica.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// TODO grab user and password from docker-compose or env vars?
		(new DockerCompose())->exec( DbReplica::SRV_DB_REPLICA, 'mysql --user=root --password=toor' );
		return 0;
	}
}
