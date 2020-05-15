<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogsTail extends Command
{

	protected static $defaultName = 'mw:logs-tail';

	protected function configure()
	{
		$this->setDescription('Tails one of the MediaWiki logs from the /var/log/mediawiki directory');
		$this->setHelp(<<< EOT
In order to output more logs to this directory you might have to enable extra log groups.
https://www.mediawiki.org/wiki/Manual:\$wgDebugLogGroups

\$wgDebugLogGroups['debug'] = "/var/log/mediawiki/debug.log";
\$wgDebugLogGroups['Wikibase'] = "/var/log/mediawiki/wikibase.log";
EOT
		);
		$this->addArgument('log', InputArgument::REQUIRED );
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$log = $input->getArgument('log');
		(new DockerCompose())->exec( Base::SRV_MEDIAWIKI, "tail -f /var/log/mediawiki/${log}");
		return 0;
	}
}
