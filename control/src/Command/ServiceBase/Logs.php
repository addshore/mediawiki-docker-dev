<?php

namespace Addshore\Mwdd\Command\ServiceBase;

use Addshore\Mwdd\DockerCompose\ServiceSet;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logs extends Command
{

	private const LOG_DEFAULT_LS = 'Lists available log files';

	protected $serviceSetName;
	protected $help;

	public function __construct( string $serviceSetName, string $help ) {
		$this->serviceSetName = $serviceSetName;
		$this->help = $help;
		parent::__construct();
	}

	protected function configure()
	{
		$serviceSet = new ServiceSet($this->serviceSetName);

		$logDir = $serviceSet->getEnvVarForFirstService('MWDD_LOG_DIR' );
		// Don't show the command if the service doesnt have a log dir configured..
		$this->setHidden(!$logDir);

		/**
		 * In order to output more logs to this directory you might have to enable extra log groups.
		https://www.mediawiki.org/wiki/Manual:\$wgDebugLogGroups

		\$wgDebugLogGroups['debug'] = "/var/log/mediawiki/debug.log";
		\$wgDebugLogGroups['Wikibase'] = "/var/log/mediawiki/wikibase.log";
		 */

		$this->setName($this->serviceSetName . ':logs');
		$this->setHelp(
			$this->help . PHP_EOL . PHP_EOL .
			'This command uses <info>docker-compose exec</info> internally to run <info>tail -f</info> in the container.'
		);
		$this->setDescription('Tail service logs' );

		$this->addArgument('log', InputArgument::OPTIONAL, '', self::LOG_DEFAULT_LS );

		$this->addUsage('');
		$this->addUsage('debug.log');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$serviceSet = new ServiceSet($this->serviceSetName);

		$log = $input->getArgument('log');

		if( $log === self::LOG_DEFAULT_LS ) {
			$output->writeln('Listing available log files:');
			(new DockerCompose())->exec( $serviceSet->getFirstServiceName(), "ls /var/log/mediawiki/");
		} else {
			(new DockerCompose())->exec( $serviceSet->getFirstServiceName(), "tail -f /var/log/mediawiki/${log}");
		}

		return 0;
	}
}
