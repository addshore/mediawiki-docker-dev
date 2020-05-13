<?php

namespace Addshore\Mwdd\Command\V0;

use Addshore\Mwdd\DockerCompose\Legacy;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ./mwdd v0:phpunit default //var/www/mediawiki/extensions/Wikibase/lib/tests/phpunit/Store/Sql/TermSqlIndexTest.php
 */
class PHPUnit extends Command
{

	protected static $defaultName = 'v0:phpunit';

	protected function configure()
	{
		$this->addArgument('phpunitArgs', InputArgument::IS_ARRAY );
		$this->ignoreValidationErrors();
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$phpunitArgs = $input->getArgument('phpunitArgs');
		$argsString = implode( ' ', $phpunitArgs );
		(new DockerCompose())->exec( Legacy::SRV_MEDIAWIKI, 'php //var/www/mediawiki/tests/phpunit/phpunit.php --wiki ' . $argsString );
		return 0;
	}
}
