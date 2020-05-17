<?php

namespace Addshore\Mwdd\Command\ServiceBase;

use Addshore\Mwdd\Command\TraitForCommandsThatAddHosts;
use Addshore\Mwdd\DockerCompose\ServiceSet;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	use TraitForCommandsThatAddHosts;

	protected $serviceSetName;
	protected $help;

	public function __construct( string $serviceSetName, string $help ) {
		$this->serviceSetName = $serviceSetName;
		$this->help = $help;
		parent::__construct();
	}

	protected function configure() {
		$this->setName($this->serviceSetName . ':create' );
		$this->setHelp(
			$this->help . PHP_EOL . PHP_EOL .
			'This command uses <info>docker-compose up -d</info> internally.'
		);
		$this->setDescription('Creates or recreates the service containers' );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$serviceSet = new ServiceSet($this->serviceSetName);
		$dotEnv = new DotEnv();
		$dotEnv->updateFromDefaultAndLocal();

		// Output which services we will be running
		$serviceNames = $serviceSet->getServiceNames();
		$output->writeln('Starting services (with deps): ' . implode( ',', $serviceNames ));
		// TODO output what docker-composer command is being run? (slim version)
		(new DockerCompose())->upDetached( $serviceNames );

		$this->addHostsAndPrintOutput( $serviceSet->getVirtualHosts(), $output );
		return 1;
	}
}
