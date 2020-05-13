<?php

namespace Addshore\Mwdd\Shell;

class DockerCompose {

	private $cmd;

	public function __construct() {
		$cmd = "docker-compose --project-directory .";
		foreach( $this->getAllYmlFiles() as $file ) {
			$cmd .= " -f ${file}";
		}
		$this->cmd = $cmd;
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
		passthru( $shell );
	}

	public function downWithVolumes() {
		$shell = $this->cmd . " down -v";
		passthru( $shell );
	}

	public function stop( array $services ) {
		$shell = $this->cmd . " stop " . implode( ' ', $services );
		passthru( $shell );
	}

	public function start( array $services ) {
		$shell = $this->cmd . " start " . implode( ' ', $services );;
		passthru( $shell );
	}

	public function exec( string $service, $command, $extraArgString = '' ) {
		$shell = $this->cmd . " exec ${extraArgString} \"${service}\" ${command}";
		passthru( $shell );
	}

	public function psQ( string $service ) {
		$shell = $this->cmd . " ps -q ${service}";
		$output = shell_exec( $shell );
		return trim( $output );
	}

	public function ps() {
		$shell = $this->cmd . " ps";
		passthru( $shell );
	}


}

