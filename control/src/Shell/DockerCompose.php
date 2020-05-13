<?php

namespace Addshore\Mwdd\Shell;

class DockerCompose {

	private const DC = "docker-compose --project-directory . -p mediawiki-docker-dev";

	public function upDetached() {
		$shell = self::DC . " up -d";
		passthru( $shell );
	}

	public function downWithVolumes() {
		$shell = self::DC . " down -v";
		passthru( $shell );
	}

	public function stop() {
		$shell = self::DC . " stop";
		passthru( $shell );
	}

	public function start() {
		$shell = self::DC . " start";
		passthru( $shell );
	}

	public function exec( string $service, $command, $extraArgString = '' ) {
		$shell = self::DC . " exec ${extraArgString} \"${service}\" ${command}";
		passthru( $shell );
	}

	public function psQ( string $service ) {
		$shell = self::DC . " ps -q ${service}";
		$output = shell_exec( $shell );
		return trim( $output );
	}


}

