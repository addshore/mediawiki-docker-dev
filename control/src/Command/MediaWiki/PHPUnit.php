<?php

namespace Addshore\Mwdd\Command\MediaWiki;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\Shell\DockerCompose;
use Addshore\Mwdd\Shell\Id;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PHPUnit extends Command
{

	protected static $defaultName = 'mw:phpunit';

	protected function configure()
	{
		$this->setDescription('Runs the MediaWiki phpunit.php');

		$this->addArgument('testPath', InputOption::VALUE_REQUIRED );
		$this->addOption('wiki', 'w', InputOption::VALUE_OPTIONAL, 'The wiki to run the tests for', 'default');
		$this->addOption('args', 'a', InputOption::VALUE_OPTIONAL, 'String of extra arguments to pass to phpunit.php', '');
		$this->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, 'Enable debugger');

		$this->addUsage('tests/phpunit/includes/StatusTest.php');
		$this->addUsage('-d=1 tests/phpunit/includes/StatusTest.php');
		$this->addUsage('tests/phpunit/includes/StatusTest.php --wiki otherwiki');
		$this->addUsage('extensions/Wikibase/lib/tests/phpunit/Store/Sql/TermSqlIndexTest.php');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$path = $input->getArgument('testPath');
		if(empty($path)) {
			$output->writeln("path must be specified");
			return 1;
		}
		$wiki = $input->getOption('wiki');
		$args = $input->getOption('args');
		$debugPrefix = '';
		if($input->getOption('debug')) {
			$debugPrefix = 'XDEBUG_CONFIG=\"remote_host=${XDEBUG_REMOTE_HOST}\" ';
		}

		$ug = (new Id())->ug();
		// This runs in the mediawiki service currently, but we could consider running it as a tool (similar to fresh etc)
		// exec instead of run so that we don't load the depends ons..... (but we could state not to load them?)
		(new DockerCompose())->exec(
			Base::SRV_MEDIAWIKI,
			"sh -c \"${debugPrefix}php //app/tests/phpunit/phpunit.php ${args} --wiki ${wiki} //app/${path}\"",
			"--user ${ug}"
		);
		return 0;
	}
}
