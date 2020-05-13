<?php

namespace Addshore\Mwdd\Files;

class MediawikiCoreDir {

	private $dir;

	public function __construct( string $dir) {
		$this->dir = $dir;
	}

	public function ensurePresent() {
		mkdir($this->dir, 0777, true);
	}
}
