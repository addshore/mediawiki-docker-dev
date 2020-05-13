<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Shell\DockerCompose;
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
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, "php //var/www/mediawiki/${script} --wiki ${wiki}");
		return 0;
	}
}
