<?php

namespace Addshore\Mwdd\Command\ServiceBase;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cli extends Command
{

	protected $serviceSetName;
	protected $cliTool;
	protected $help;

	public function __construct( string $serviceSetName, string $cliTool, string $help ) {
		$this->serviceSetName = $serviceSetName;
		$this->cliTool = $cliTool;
		$this->help = $help;
		parent::__construct();
	}

	protected function configure() {
		$this->setName($this->serviceSetName . ':cli');
		$this->setHelp(
			$this->help . PHP_EOL . PHP_EOL .
			'This command uses <info>docker-compose exec</info> internally.'
		);
		$this->setDescription('Runs the cli tool for this service in a container' );

		$this->addUsage('');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		return $this->getApplication()->find($this->serviceSetName . ':exec')->run( new ArrayInput([ Exec::COMMAND => $this->cliTool ]), $output );
	}
}
