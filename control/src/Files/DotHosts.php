<?php

namespace Addshore\Mwdd\Files;

class DotHosts {

	private const FILE = MWDD_DIR . '/.hosts';
	private const SUFFIX = "# mediawiki-docker-dev";

	public function addHost( string $ip, string $host ) {
		$this->addLine( "${ip} ${host} " . self::SUFFIX );
	}

	private function ensureFileExists() {
		touch(self::FILE);
	}

	private function addLine( string $line ) {
		$this->ensureFileExists();
		file_put_contents(self::FILE, trim($line).PHP_EOL , FILE_APPEND | LOCK_EX);
	}

}
