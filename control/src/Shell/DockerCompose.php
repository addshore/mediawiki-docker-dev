<?php

namespace Addshore\Mwdd\Shell;

use Addshore\Mwdd\Files\DotEnv;

class DockerCompose {

	private $cmd;

	public function __construct() {
		$cmd = "docker-compose --project-directory . -p mediawiki-docker-dev";
		foreach( $this->getAllYmlFiles() as $file ) {
			$cmd .= " -f ${file}";
		}
		$this->cmd = $cmd;
	}

	/**
	 * The .env file must exist to run docker-compose commands without it screaming WARNING for no env vars..
	 * This would often be a problem on first initialization
	 */
	private function ensureDotEnv() {
		$dotEnv = new DotEnv();
		if(!$dotEnv->exists()) {
			$dotEnv->updateFromDefaultAndLocal();
		}
	}

	/**
	 * @param string $fullCommand including docker-compose
	 */
	private function passthruDc( string $fullCommand ) {
		$this->ensureDotEnv();
		passthru( $fullCommand );
	}

	/**
	 * @return string[] of files for example "docker-compose/foo.yml"
	 */
	private function getAllYmlFiles () : array {
		$files = array_diff(scandir('docker-compose'), array('.', '..'));
		array_walk( $files, function( &$value ) {
			$value = 'docker-compose/' . $value;
		} );
		return $files;
	}

	/**
	 * @param string[] $services
	 * @param bool $build Should the --build flag be passed?
	 */
	public function upDetached( array $services, $build = false ) {
		$buildString = $build ? ' --build' : '';
		$shell = $this->cmd . " up -d ${buildString} " . implode( ' ', $services );
		$this->passthruDc( $shell );
	}

	public function downWithVolumes() {
		$shell = $this->cmd . " down -v";
		$this->passthruDc( $shell );
	}

	public function stop( array $services ) {
		$shell = $this->cmd . " stop " . implode( ' ', $services );
		$this->passthruDc( $shell );
	}

	public function start( array $services ) {
		$shell = $this->cmd . " start " . implode( ' ', $services );;
		$this->passthruDc( $shell );
	}

	// Command in an already running container
	public function exec( string $service, $command, $extraArgString = '' ) {
		$shell = $this->cmd . " exec ${extraArgString} \"${service}\" ${command}";
		$this->passthruDc( $shell );
	}

	// Command in a new container
	public function run( string $service, $command, $extraArgString = '' ) {
		$shell = $this->cmd . " run ${extraArgString} \"${service}\" ${command}";
		$this->passthruDc( $shell );
	}

	// Command in a new container
	public function runDetatched( string $service, $command, $extraArgString = '' ) {
		$shell = $this->cmd . " run -d ${extraArgString} \"${service}\" ${command}";
		// TODO should this actually passthru, given it is detached??
		$this->passthruDc( $shell );
	}

	public function psQ( string $service ) {
		$shell = $this->cmd . " ps -q ${service}";
		$output = shell_exec( $shell );
		return trim( $output );
	}

	public function ps() {
		$shell = $this->cmd . " ps";
		$this->passthruDc( $shell );
	}

	public function logsTail( string $service, int $lines = 25 ) {
		$shell = $this->cmd . " logs --tail=${lines} -f ${service}";
		$this->passthruDc( $shell );
	}

	public function raw( string $rawCommand ) {
		$shell = $this->cmd . " ${rawCommand}";
		$this->passthruDc( $shell );
	}


}

