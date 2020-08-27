<?php

namespace Addshore\Mwdd\Command\ServiceBase;

use Addshore\Mwdd\DockerCompose\ServiceSet;
use Addshore\Mwdd\Shell\DockerCompose;
use Addshore\Mwdd\Shell\Id;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Exec extends Command
{

	public const COMMAND = 'COMMAND';
	public const SERVICE = 'SERVICE';
	public const USER = 'user';

	protected $serviceSetName;
	protected $help;

	protected const DEFAULT_COMMAND = 'Auto detect shell (sh/bash)';
	protected const DEFAULT_SERVICE = 'Primary / first service';
	protected const DEFAULT_USER = 'Host machine user';

	public function __construct( string $serviceSetName, string $help ) {
		$this->serviceSetName = $serviceSetName;
		$this->help = $help;
		parent::__construct();
	}

	protected function configure() {
		$this->setName($this->serviceSetName . ':exec');
		$this->setHelp(
			$this->help . PHP_EOL . PHP_EOL .
			'This command uses <info>docker-compose exec</info> internally.'
		);
		$this->setDescription('Runs a command a running service container' );

		$this->addArgument(
			self::COMMAND,
			null,
			'Which COMMAND to run inside the container.',
			self::DEFAULT_COMMAND
		);
		$this->addArgument(
			self::SERVICE,
			null,
			'Which container to run the COMMAND inside of? Defaults to the primary service.',
			self::DEFAULT_SERVICE
		);

		$this->addOption(
			self::USER,
			null,
			InputOption::VALUE_OPTIONAL,
			'Which user to run as',
			self::DEFAULT_USER
		);

		$this->addUsage('');
		$this->addUsage('bash');
		$this->addUsage('--user=root bash');
		$this->addUsage('sh');
		$this->addUsage('sh other-service');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$serviceSet = new ServiceSet($this->serviceSetName);

		$command = $input->getArgument( self::COMMAND );
		if($command === self::DEFAULT_COMMAND) {
			// use sh which we probably have to detect if bash is there and use the best one
			$command = 'sh -c "if [ $(command -v bash) ]; then bash; else sh; fi"';
		}

		$service = $input->getArgument( self::SERVICE );
		if($service === self::DEFAULT_SERVICE) {
			// Get the first service listed in the docker-compose yml which should be the most important
			$service = $serviceSet->getServiceNames()[0];
		}

		$user = $input->getOption( self::USER );
		if($user === self::DEFAULT_USER) {
			$output->writeln('MWDD: You may see a message saying the user or group ID doesnt exist, but you can ignore it');
			$user = (new Id())->ug();
		}

		(new DockerCompose())->execIt( $service, $command, '--user ' . $user );

		return 0;
	}
}
