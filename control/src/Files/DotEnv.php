<?php

namespace Addshore\Mwdd\Files;

use M1\Env\Parser;

class DotEnv {

	private const FILE = MWDD_DIR . '/.env';
	private const DEFAULT = MWDD_DIR . '/default.env';
	private const LOCAL = MWDD_DIR . '/local.env';

	public function exists() : bool {
		return file_exists(self::FILE);
	}

	/**
	 * Combines the default.env and local.env files for the environment into a single .env file
	 */
	public function updateFromDefaultAndLocal() {
		$defaultEnv = (new Parser(file_get_contents(self::DEFAULT)));
		$localEnv = (new Parser(file_get_contents(self::LOCAL)));

		$combinesLines = array_merge(
			$defaultEnv->lines,
			$localEnv->lines
		);

		$combinesLines = $this->swapOutValues( $combinesLines );

		$finalLines = '';
		foreach( $combinesLines as $key => $line ) {
			$finalLines .= $key . '=' . "${line}" . PHP_EOL;
		}

		file_put_contents( self::FILE, $finalLines );
	}

	private function swapOutValues( array $combinesLines ) {
		if($combinesLines['UID'] === '{{id -u}}'){
			$combinesLines['UID'] = trim(shell_exec('id -u'));
		}
		if($combinesLines['GID'] === '{{id -g}}'){
			$combinesLines['GID'] = trim(shell_exec('id -g'));
		}
		return $combinesLines;
	}

}
