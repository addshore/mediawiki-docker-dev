<?php

namespace Addshore\Mwdd\Command\PhpMyAdmin;

use Addshore\Mwdd\DockerCompose\PhpMyAdmin;
use Addshore\Mwdd\Files\DotEnv;
use Addshore\Mwdd\Shell\DockerCompose;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{

	protected static $defaultName = 'phpmyadmin:create';

	protected function configure()
	{
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		(new DotEnv())->updateFromDefaultAndLocal();

		$output->writeln('Starting services: ' . implode( ',', PhpMyAdmin::SERVICES ));
		(new DockerCompose())->upDetached( PhpMyAdmin::SERVICES );

		// TODO output correct domain based on configuration
		$output->writeln('You can access phpmyadmin at: http://phpmyadmin.mw.localhost:8080/ (if using default settings)');
		$output->writeln('The default username and password are "root" and "toor"');

		$this->getApplication()->find('v0:hosts-add')->run( new ArrayInput([ 'host' => 'phpmyadmin.mw.localhost' ]), $output );
		$output->writeln('You may need to update your hosts file (see .hosts and hosts-sync files)!!');

		return 0;

	}
}
