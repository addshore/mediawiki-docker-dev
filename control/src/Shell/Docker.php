<?php

namespace Addshore\Mwdd\Shell;

class Docker {

	private const D = "docker";

	public function cp( string $source, string $target ) {
		$shell = self::D . " cp ${source} ${target}";
		passthru( $shell );
	}

}

