<?php

namespace Addshore\Mwdd\Command\MediaWiki;

use Addshore\Mwdd\DockerCompose\MwComposer;
use Addshore\Mwdd\DockerCompose\MwFresh;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Fresh extends Command
{

	protected static $defaultName = 'mw:fresh';

	protected function configure()
	{
		$this->setDescription('Runs fresh for MediaWiki or a subdirectory (when using the alias)');
		$this->addUsage('npm install');
		$this->addUsage('npm run selenium');
		$this->setHelp( <<< EOT
Runs 'fresh', a node js running environment, within the MediaWiki context.
See: https://github.com/wikimedia/fresh

By default this will run in the MediaWiki core directory.

If you are using the recommended mwdd alias this command will try to run composer in the context of the directory you run the command from.
This is only relevant when said directory is within the mediawiki core directory.
EOT
		);

		$this->addArgument('args', InputArgument::IS_ARRAY );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Try to run in the correct directory to run the command in
		// TODO reuse this code somewhere....
		$shortcutEnv = getenv('MWDD_S_DIR');
		if($shortcutEnv) {
			$mwPath = (new DotEnv())->getValue('DOCKER_MW_PATH');
			// Trim /'s from the start and end, and ~ if used as the MW path base
			$shortcutEnv = trim( $shortcutEnv, '/' );
			$mwPath = trim( $mwPath, '/~' );
			// Determine some stuff
			$isInMwDir = strstr( $shortcutEnv, $mwPath );
			if($isInMwDir) {
				$splitOnMwPath = explode( $mwPath, $shortcutEnv );
				$pathInsideMwPath = $splitOnMwPath[1];
			}
		} else {
			$pathInsideMwPath = '';
		}

		$args = $input->getArgument('args');

		// The service must be created in order to be able to use docker run
		// TODO don't always run this...
		$output->writeln("MWDD: Sorry that this is a bit slow to run (need to think of a nice fix) as it runs up each time");
		$output->writeln("MWDD: Fresh also seems to be a bit buggy currently and sometimes freeze? maybe?");
		(new DockerCompose())->upDetached(MwFresh::SERVICES);


		// TODO needs addslashes a little for " ?
		if(strstr(implode( ' ', $args ), '"')) {
			$output->writeln('MWDD: WARNING, Your arguments have a " in them, I currently predict something will go wrong.');
		}

		(new DockerCompose())->run(
			MwFresh::SRV_FRESH,
			// TODO needs addslashes a little for " ?
			'-c "' . implode( ' ', $args ) . '"',
			'--rm --workdir=/app/' . $pathInsideMwPath
		);
		return 0;
	}
}
