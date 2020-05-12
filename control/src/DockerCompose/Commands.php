<?php

namespace Addshore\Mwdd\DockerCompose;

class Commands {

	private const DC = "docker-compose --project-directory . -p mediawiki-docker-dev";

	public function stop() {
		$shell = self::DC . " stop";
		passthru( $shell );
	}

	public function start() {
		$shell = self::DC . " start";
		passthru( $shell );
	}

	public function exec( string $service, $command ) {
		$shell = self::DC . " exec \"${service}\" ${command}";
		passthru( $shell );
	}

}

