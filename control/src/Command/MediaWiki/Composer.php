<?php

namespace Addshore\Mwdd\Command\MediaWiki;

use Addshore\Mwdd\DockerCompose\MwComposer;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Composer extends Command
{

	protected static $defaultName = 'mw:composer';

	protected function configure()
	{
		$this->addArgument('args', InputArgument::IS_ARRAY );
		$this->addUsage('version');
		$this->addUsage('update --ignore-platform-reqs');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$args = $input->getArgument('args');

		// The service must be created in order to be able to use docker run
		// TODO don't always run this...
		$output->writeln("MWDD: Sorry that this is a bit slow to run (need to think of a nice fix)");
		(new DockerCompose())->upDetached(MwComposer::SERVICES);

		(new DockerCompose())->run(
			MwComposer::SRV_COMPOSER,
			"composer " . implode( ' ', $args )
		);
		return 0;
	}
}
