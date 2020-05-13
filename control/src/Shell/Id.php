<?php

namespace Addshore\Mwdd\Shell;

class Id {

	private const I = "id";

	public function ug() {
		return $this->u() . ':' . $this->g();
	}

	public function u() {
		$shell = self::I . " -u";
		return trim( shell_exec( $shell ) );
	}

	public function g() {
		$shell = self::I . " -g";
		return trim( shell_exec( $shell ) );
	}

}
