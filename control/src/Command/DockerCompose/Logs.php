<?php

namespace Addshore\Mwdd\Command\DockerCompose;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logs extends Command
{

	protected static $defaultName = 'dc:logs';

	protected function configure()
	{
		$this->setDescription('Tails service logs.');
		$this->addArgument( 'service' );

	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$service = $input->getArgument('service');

		(new DockerCompose())->logsTail( $service );
		return 0;
	}
}
