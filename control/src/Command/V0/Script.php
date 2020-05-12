<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Script extends Command
{

	protected static $defaultName = 'v0:script';

	protected function configure()
	{
		$this->addArgument('wiki' );
		$this->addArgument('script' );
		$this->ignoreValidationErrors();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$wiki = $input->getArgument('wiki');
		$script = $input->getArgument('script');
		(new Commands())->exec( 'web', "php //var/www/mediawiki/${script} --wiki ${wiki}");
	}
}
