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
		// TODO make this ALWAYS mount the directory...?
		// Otherwise the following happens...
		// Cannot create cache directory /tmp/cache/repo/https---repo.packagist.org/, or directory is not writable. Proceeding without cache
		// Cannot create cache directory /tmp/cache/files/, or directory is not writable. Proceeding without cache
		if( file_exists( getenv('HOME') . '/.composer' ) ) {
			// Note: this relies on the fact that the COMPOSER_HOME value is set to /tmp in the image by default.
			$homeComposerMntString = "-v " . getenv('HOME') . '/.composer' . ":/tmp";
		}

		// This runs with the running user id, which is good and means no chown is needed...
		$shell = self::D . " run -it --rm --user $(id -u):$(id -g) ${homeComposerMntString} -v ${dir}:/app composer install --ignore-platform-reqs";
		passthru( $shell );
	}

}

