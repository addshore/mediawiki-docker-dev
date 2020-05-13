<?php

namespace Addshore\Mwdd\Shell;

class Git {

	private const G = "echo git";

	public function clone( string $repo, string $target, ?int $depth = 1 ) {
		$depthString = $depth ? "--depth ${depth}" : "";
		$shell = self::G . " clone ${depthString} ${repo} $target";
		passthru( $shell );
	}

}
