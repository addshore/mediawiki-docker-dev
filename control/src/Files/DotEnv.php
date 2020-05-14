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

	public function getValue(string $key) : ?string {
		if(!$this->exists()) {
			$this->updateFromDefaultAndLocal();
		}
		$dotEnv = (new Parser(file_get_contents(self::FILE)));
		return $dotEnv->getContent($key);
	}

	/**
	 * Combines the default.env and local.env files for the environment into a single .env file
	 */
	public function updateFromDefaultAndLocal() {
		$defaultEnv = (new Parser(file_get_contents(self::DEFAULT)));

		// Sometimes users will not have specified a local env file just yet...
		if(file_exists(self::LOCAL)) {
			$localEnv = (new Parser(file_get_contents(self::LOCAL)));
			$finalEnvLines = array_merge(
				$defaultEnv->lines,
				$localEnv->lines
			);
		} else {
			$finalEnvLines = $defaultEnv->lines;
		}

		$finalEnvLines = $this->swapOutValues( $finalEnvLines );

		$finalLines = '';
		foreach( $finalEnvLines as $key => $line ) {
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
