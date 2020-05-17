<?php

namespace Addshore\Mwdd\Command;

use Addshore\Mwdd\Files\DotEnv;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

trait TraitForCommandsThatAddHosts {

	private $addedHosts = [];

	protected function addHostsAndPrintOutput( array $hosts, OutputInterface $output ) : void{
		foreach( $hosts as $host ) {
			$this->addHost( $host, $output );
		}
		$this->printInfoAboutAddedHosts( $output );
	}

	protected function addHosts( array $hosts, OutputInterface $output ) : void{
		foreach( $hosts as $host ) {
			$this->addHost( $host, $output );
		}
	}

	protected function addHost( string $host, OutputInterface $output ) :void {
		$this->getApplication()->find('base:hosts-add')->run( new ArrayInput([ 'host' => $host ]), $output );
		$this->addedHosts[] = $host;
	}

	protected function printInfoAboutAddedHosts( OutputInterface $output ): void {
		if( !$this->addedHosts ) {
			return;
		}

		$dotEnv = new DotEnv();
		$dotEnv->updateFromDefaultAndLocal();
		$publicPort = $dotEnv->getValue('DOCKER_MW_PORT');

		$output->writeln('Some processes added new host (accessible via HTTP):');
		foreach( $this->addedHosts as $host ) {
			$output->writeln( " - <href=http://${host}:${publicPort}>http://${host}:${publicPort}</>");
		}
	}

}
