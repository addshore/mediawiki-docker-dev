<?php

namespace Addshore\Mwdd\Command\V0;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HostsAdd extends Command
{

	protected static $defaultName = 'v0:hosts-add';

	protected function configure()
	{
		$this->addArgument( 'host' );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$host = $input->getArgument( 'host' );
		$shell = "echo 127.0.0.1 ${host} # mediawiki-docker-dev >> .hosts";
		passthru( $shell );
	}
}
