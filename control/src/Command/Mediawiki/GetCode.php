<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Files\MediawikiCoreDir;
use Addshore\Mwdd\Shell\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GetCode extends Command
{

	protected static $defaultName = 'mw:getcode';

	protected function configure()
	{
		// TODO hide if code already exists.. maybe?
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');

		$dotEnv = new DotEnv();
		$dotEnv->updateFromDefaultAndLocal();
		$mwPath = $dotEnv->getValue('DOCKER_MW_PATH');

		$output->writeln('Current MediaWiki path: ' . $mwPath);
		$question = new ConfirmationQuestion('Is this the path that you want to fetch MediaWiki to?', false);
//		if (!$helper->ask($input, $output, $question)) {
//			$output->writeln("Please update your local.env file before continuing.");
//			return 0;
//		}

		var_dump(file_exists( realpath($mwPath . '/.git') ));die();
		if(file_exists( $mwPath . '/.git' )) {
			$output->writeln( 'It looks like mediawiki is already in the requested directory. bailing...' );
			return 0;
		}

		$mwCoreDir = new MediawikiCoreDir( $mwPath );
		$mwCoreDir->ensurePresent();
		die('tried to clone..');
		( new Git() )->clone( 'https://gerrit.wikimedia.org/r/mediawiki/core', $mwPath );
		( new Git() )->clone( 'https://gerrit.wikimedia.org/r/mediawiki/skins/Vector',
			$mwPath . '/skins/Vector' );

		return 0;
	}
}
