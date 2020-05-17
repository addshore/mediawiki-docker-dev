<?php

namespace Addshore\Mwdd\Command\Mediawiki;

use Addshore\Mwdd\DockerCompose\Base;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Maint extends Command
{

	protected static $defaultName = 'mw:maint';

	protected function configure()
	{
		$this->setDescription('Runs a MediaWiki maintenance script');

		$this->addArgument('script', InputArgument::REQUIRED | InputArgument::IS_ARRAY );
		$this->addOption('wiki', null, InputArgument::OPTIONAL, '', 'default' );
		$this->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, 'Enable debugger');
		$this->ignoreValidationErrors();

		$this->addUsage('-- maintenance/showJobs.php');
		$this->addUsage('-- maintenance/showJobs.php --group');
		$this->addUsage('--wiki=other -- maintenance/showJobs.php');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$debugPrefix = '';
		if($input->getOption('debug')) {
			$debugPrefix = 'XDEBUG_CONFIG=\"remote_host=${XDEBUG_REMOTE_HOST}\" ';
		}

		$wiki = $input->getOption('wiki');
		$script = implode( ' ', $input->getArgument('script') );
		(new DockerCompose())->exec(
			Base::SRV_MEDIAWIKI,
			"sh -c \"${debugPrefix}php //app/${script} --wiki ${wiki}\""
		);
		return 0;
	}
}
