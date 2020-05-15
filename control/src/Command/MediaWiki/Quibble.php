<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Quibble extends Command
{

	protected static $defaultName = 'mw:quibble';

	protected function configure()
	{
		$this->setDescription('NOT YET IMPLEMENTED');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Not Yet Implemented!");
		return 0;
	}
}
