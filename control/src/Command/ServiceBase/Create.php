<?php

namespace Addshore\Mwdd\Command\ServiceBase;

use Addshore\Mwdd\DockerCompose\ServiceSet;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

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
		$publicPort = $dotEnv->getValue('DOCKER_MW_PORT');

		// Output which services we will be running
		$serviceNames = $serviceSet->getServiceNames();
		$output->writeln('Starting services: ' . implode( ',', $serviceNames ));
		// TODO output what docker-composer command is being run? (slim version)
		(new DockerCompose())->upDetached( $serviceNames );

		// Output about vhosts for access
		$virtualHosts = $serviceSet->getVirtualHosts();
		if( !empty($virtualHosts) ) {
			$output->writeln('Some of the services can be accessed via HTTP.');
			foreach( $virtualHosts as $host ) {
				$output->writeln( " - <href=http://${host}:${publicPort}>http://${host}:${publicPort}</>");
				// TODO don't use the v0 command here
				// TODO optionally output exactly what is happening here? the command should have output...
				$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => $host ]), $output );

			}
			$output->writeln('You may need to update your hosts file (see .hosts and hosts-sync files)!!');
		}
		return 1;
	}
}
