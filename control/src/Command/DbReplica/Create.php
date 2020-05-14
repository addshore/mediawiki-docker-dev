<?php

namespace Addshore\Mwdd\Command\DbReplica;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\DockerCompose\DbReplica;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	protected static $defaultName = 'db:replica:create';

	protected function configure()
	{
		$this->setDescription('Creates a db replica, automatically replicating from the master.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DotEnv())->updateFromDefaultAndLocal();

		$output->writeln('Starting services: ' . implode( ',', DbReplica::SERVICES ));
		(new DockerCompose())->upDetached( DbReplica::SERVICES );

		# Ask the db-configure container to setup the replication
		(new DockerCompose())->runDetatched( Base::SRV_DB_CONFIGURE, '/bin/bash -x /mwdd-scripts/mysql_connector_replica.sh' );

		# Wait for the replica db server
		$output->writeln('Waiting for the db server');
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, '//srv/wait-for-it.sh db-replica:3306' );

		# TODO provide a way to look at replag in the CLI? OR wait for it here?
		$output->writeln('It might take a few seconds for DB replication to catch up');

		return 0;
	}
}
