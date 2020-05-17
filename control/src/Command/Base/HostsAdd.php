<?php

namespace Addshore\Mwdd\Command\Base;

use Addshore\Mwdd\Files\DotHosts;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HostsAdd extends Command
{

	protected static $defaultName = 'base:hosts-add';

	protected function configure()
	{
		$this->setHidden(true);
		$this->addArgument( 'host' );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$host = $input->getArgument( 'host' );
		(new DotHosts())->addHost( '127.0.0.1', $host );
		return 0;
	}
}
