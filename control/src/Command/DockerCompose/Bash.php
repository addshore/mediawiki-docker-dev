<?php

namespace Addshore\Mwdd\Command\DockerCompose;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Bash extends Command
{

	protected static $defaultName = 'dc:bash';

	protected function configure()
	{
		$this->setDescription('Run a shell on one of the service containers');
		$this->addArgument( 'service' );

	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$service = $input->getArgument('service');

		// TODO pick bash or sh?
		(new DockerCompose())->exec( $service, "sh" );
		return 0;
	}
}
