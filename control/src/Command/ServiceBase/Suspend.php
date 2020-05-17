<?php

namespace Addshore\Mwdd\Command\ServiceBase;

use Addshore\Mwdd\DockerCompose\ServiceSet;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Suspend extends Command
{

	protected $serviceSetName;
	protected $help;

	public function __construct( string $serviceSetName, string $help ) {
		$this->serviceSetName = $serviceSetName;
		$this->help = $help;
		parent::__construct();
	}

	protected function configure() {
		$this->setName($this->serviceSetName . ':suspend');
		$this->setHelp(
			$this->help . PHP_EOL . PHP_EOL .
			'This command uses <info>docker-compose stop</info> internally.'
		);
		$this->setDescription('Suspends a set of previously created containers' );

	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$serviceSet = new ServiceSet($this->serviceSetName);
		(new DockerCompose())->stop( $serviceSet->getServiceNames());
		return 0;
	}
}
