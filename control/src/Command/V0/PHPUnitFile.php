<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ./mwdd v0:phpunit-file default extensions/Wikibase/lib/tests/phpunit/Store/Sql/TermSqlIndexTest.php
 */
class PHPUnitFile extends Command
{

	protected static $defaultName = 'v0:phpunit-file';

	protected function configure()
	{
		$this->addArgument('wiki' );
		$this->addArgument('path' );
		$this->ignoreValidationErrors();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$wiki = $input->getArgument('wiki');
		$path = $input->getArgument('path');
		(new DockerCompose())->exec( 'web', "php //var/www/mediawiki/tests/phpunit/phpunit.php --wiki ${wiki} //var/www/mediawiki/"  . $path );
		return 0;
	}
}
