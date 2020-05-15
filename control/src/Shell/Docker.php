<?php

namespace Addshore\Mwdd\Shell;

class Docker {

	private const D = "docker";

	public function cp( string $source, string $target ) {
		$shell = self::D . " cp ${source} ${target}";
		passthru( $shell );
	}

	public function runComposerInstall( string $dir ) {
		// TODO should this method be somewhere else?
		$homeComposerMntString = "";
		if( file_exists( getenv('HOME') . '/.composer' ) ) {
			// Note: this relies on the fact that the COMPOSER_HOME value is set to /tmp in the image by default.
			$homeComposerMntString = "-v " . getenv('HOME') . '/.composer' . ":/tmp";
		}

		// This runs with the running user id, which is good and means no chown is needed...
		$shell = self::D . " run -it --rm --user $(id -u):$(id -g) ${homeComposerMntString} -v ${dir}:/app composer install --ignore-platform-reqs";
		passthru( $shell );
	}

}

