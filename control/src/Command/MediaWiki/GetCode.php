<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Files\MediaWikiDir;
use Addshore\Mwdd\Shell\Docker;
use Addshore\Mwdd\Shell\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GetCode extends Command {

	protected static $defaultName = 'mw:getcode';

	protected function configure() {
		$this->setDescription('Gets MediaWiki code if you don\'t already have it.');
		// TODO right now this doesnt auto gen the file if it already exists... so just always show the command, maybe we need a different config? :D
		$this->setHidden((new MediaWikiDir((new DotEnv(true))->getValue('DOCKER_MW_PATH')))->hasDotGitDirectory());
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$mwPath = (new DotEnv())->getValue('DOCKER_MW_PATH');

		$output->writeln("Your currently configured MediaWiki path is: " . $mwPath);
		$helper = $this->getHelper('question');
		$question = new ConfirmationQuestion('Would you like to fetch code there??', false);

		if (!$helper->ask($input, $output, $question)) {
			$output->writeln("Please update your local.env before continuing.");
			return 0;
		}

		// TODO is this shell conversion needed elsewhere.. YES (to get rid of ~)?
		$dir = trim(shell_exec('realpath ' . $mwPath));
		$mwDir = new MediaWikiDir( $dir );

		// Clone the minimum needed code
		$mwDir->ensurePresent();
		( new Git() )->clone( 'https://gerrit.wikimedia.org/r/mediawiki/core', $mwDir );
		( new Git() )->clone( 'https://gerrit.wikimedia.org/r/mediawiki/skins/Vector',
			$dir
			. '/mediawiki/skins/Vector' );

		// Run composer install (not as part of compose)
		( new Docker() )->runComposerInstall( $mwDir );

		// Create the basic local settings file...
		$lsFile = $mwDir . '/LocalSettings.php';
		$initialLocalSettings = <<<EOT
<?php
require_once __DIR__ . '/.docker/LocalSettings.php';
wfLoadSkin( 'Vector' );
EOT;
		file_put_contents( $lsFile, $initialLocalSettings );

		return 0;
	}

}
