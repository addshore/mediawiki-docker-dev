<?php

namespace Addshore\Mwdd\Command;

use Addshore\Mwdd\DockerCompose\ServiceSet;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServiceSetBase extends Command
{

	public const CREATE = 'create';
	public const SUSPEND = 'suspend';
	public const RESUME = 'resume';
	public const SHELL = 'shell';
	protected const ACTIONS = [
		self::CREATE,
		self::SUSPEND,
		self::RESUME,
		self::SHELL,
	];

	protected $serviceSetName;
	protected $action;

	public function __construct( string $serviceSetName, string $action ) {
		$this->serviceSetName = $serviceSetName;
		$this->action = $action;
		parent::__construct();
	}

	protected function configure() {
		$this->setName($this->serviceSetName . ( $this->action ? ':' . $this->action : '' ));
		$this->setHelp(<<<EOH
Adminer is a tool for managing content in MySQL databases.
EOH
);

		switch ( $this->action ) {
			case self::CREATE;
				$this->setDescription('Creates or recreates the service containers' );
				break;
			case self::SUSPEND;
				$this->setDescription('Suspends a set of previously created containers' );
				break;
			case self::RESUME;
				$this->setDescription('Resumes a set of previously created containers' );
				break;
			case self::SHELL;
				$this->setDescription('Runs a shell in the main container (either sh of bash)' );
				$this->addArgument('shell', null, 'Which shell to use, probably sh or bash (override auto detection).');
				break;
		}
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$serviceSet = new ServiceSet($this->serviceSetName);
		switch ( $this->action ) {
			case self::CREATE;
				return $this->doCreate( $serviceSet, $output );
			case self::SUSPEND;
				return $this->doSuspend( $serviceSet, $output );
			case self::RESUME;
				return $this->doResume( $serviceSet, $output );
			case self::SHELL;
				return $this->doShell( $serviceSet, $output );
		}
		return 1;
	}

	protected function doCreate( ServiceSet $serviceSet, OutputInterface $output ) : int {
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

		return 0;
	}

	private function doSuspend( ServiceSet $serviceSet, OutputInterface $output ) {
		(new DockerCompose())->stop( $serviceSet->getServiceNames());
		return 0;
	}

	private function doResume( ServiceSet $serviceSet, OutputInterface $output ) {
		(new DockerCompose())->start( $serviceSet->getServiceNames());
		return 0;
	}

	private function doShell( ServiceSet $serviceSet, OutputInterface $output ) {
		// Do this for the first service only right now (...)
		// This will pick either sh or bash depending onw hat exists..
		(new DockerCompose())->exec( $serviceSet->getServiceNames()[0], 'sh -c "if [ $(command -v bash) ]; then bash; else sh; fi"' );
		return 0;
	}
}
