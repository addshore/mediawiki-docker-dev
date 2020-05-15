<?php

namespace Addshore\Mwdd\Command\MediaWiki;

use Addshore\Mwdd\DockerCompose\MwComposer;
use Addshore\Mwdd\DockerCompose\MwFresh;
use Addshore\Mwdd\DockerCompose\MwQuibble;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Quibble extends Command
{

	protected static $defaultName = 'mw:quibble';

	protected function configure()
	{
		$this->addArgument('args', InputArgument::IS_ARRAY );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Try to run in the correct directory to run the command in
		// TODO reuse this code somewhere....
//		$shortcutEnv = getenv('MWDD_S_DIR');
		$pathInsideMwPath = '';
//		if($shortcutEnv) {
//			$mwPath = (new DotEnv())->getValue('DOCKER_MW_PATH');
//			// Trim /'s from the start and end, and ~ if used as the MW path base
//			$shortcutEnv = trim( $shortcutEnv, '/' );
//			$mwPath = trim( $mwPath, '/~' );
//			// Determine some stuff
//			$isInMwDir = strstr( $shortcutEnv, $mwPath );
//			if($isInMwDir) {
//				$splitOnMwPath = explode( $mwPath, $shortcutEnv );
//				$pathInsideMwPath = $splitOnMwPath[1];
//			}
//		}

		$args = $input->getArgument('args');

		// The service must be created in order to be able to use docker run
		// TODO don't always run this...
		$output->writeln("MWDD: Sorry that this is a bit slow to run (need to think of a nice fix) as it runs up each time");
		$output->writeln("MWDD: Quibble also han't been tested at all");
		(new DockerCompose())->upDetached(MwQuibble::SERVICES);


		// TODO needs addslashes a little for " ?
		if(strstr(implode( ' ', $args ), '"')) {
			$output->writeln('MWDD: WARNING, Your arguments have a " in them, I currently predict something will go wrong.');
		}

		(new DockerCompose())->run(
			MwQuibble::SRV_QUIBBLE,
			" --skip-zuul --skip-deps --skip-install " . implode( ' ', $args ),
			'--rm --entrypoint=/usr/local/bin/quibble --workdir=/app/' . $pathInsideMwPath
		);
		return 0;
	}
}
