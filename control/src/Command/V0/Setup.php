<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\Files\MediawikiCoreDir;
use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Setup extends Command {

	protected static $defaultName = 'v0:setup';

	protected function configure() {
		$this->addArgument( 'dir', null, 'Directory for mediawiki core code' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$dir = $input->getArgument( 'dir' );
		$installDir = new MediawikiCoreDir( $dir );

		// Clone the minimum needed code
		$installDir->ensurePresent();
		( new Git() )->clone( 'https://gerrit.wikimedia.org/r/mediawiki/core', $dir );
		( new Git() )->clone( 'https://gerrit.wikimedia.org/r/mediawiki/skins/Vector',
			$dir . '/skins/Vector' );

		// Run composer install
		( new Docker() )->runComposerInstall( $dir );

		// Create the basic local settings file...
		$lsFile = $dir . '/LocalSettings.php';
		$initialLocalSettings = <<<EOT
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
wfLoadSkin( 'Vector' );
EOT;
		file_put_contents( $lsFile, $initialLocalSettings );
	}

}
