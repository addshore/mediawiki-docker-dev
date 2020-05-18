<?php

namespace Addshore\Mwdd\Command\MediaWiki;

use Addshore\Mwdd\DockerCompose\MwComposer;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Composer extends Command
{

	protected static $defaultName = 'mw:composer';

	protected function configure()
	{
		$this->setDescription('Runs composer for mediawiki or a subdirectory (when using the alias)');
		$this->setHelp( <<< EOT
Runs the composer command in a container within the MediaWiki context.
By default this will run in the MediaWiki core directory.

If you are using the recommended mwdd alias this command will try to run composer in the context of the directory you run the command from.
This is only relevant when said directory is within the mediawiki core directory.

Commands that rely on other applications, such as git, will NOT work as expected.
An example of this would be the Wikibase phpcs-modified script.
EOT
		);
		$this->addUsage('version');
		$this->addUsage('update --ignore-platform-reqs');

		$this->addArgument('args', InputArgument::IS_ARRAY );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Try to run in the correct directory to run the command in
		// TODO reuse this code somewhere....
		$shortcutEnv = getenv('MWDD_S_DIR');
		$pathInsideMwPath = '';
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
		}

		$args = $input->getArgument('args');

		// The service must be created in order to be able to use docker run
		// TODO don't always run this...
		$output->writeln("MWDD: Sorry that this is a bit slow to run (need to think of a nice fix) as it runs up each time");
		(new DockerCompose())->upDetached(MwComposer::SERVICES);

		// TODO mount local .composer cache dir?! (done in getCode a bit already)
		// User is specified in the docker-compose yml
		(new DockerCompose())->run(
			MwComposer::SRV_COMPOSER,
			"composer " . implode( ' ', $args ),
			'--rm --workdir=/app/' . $pathInsideMwPath
		);
		return 0;
	}
}
