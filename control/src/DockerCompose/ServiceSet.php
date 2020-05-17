<?php

namespace Addshore\Mwdd\DockerCompose;

use Addshore\Mwdd\Files\DotEnv;
use M1\Env\Parser;
use Symfony\Component\Yaml\Yaml;

/**
 * A service set represents a docker-compose.yml file with a set of services within it.
 * These services should be designed to fit into the mwdd espectations, which means:
 *  - dns configured?
 *  - VHOST exposed if desired?
 *  - matching docker-compose version....
 */
class ServiceSet {

	/**
	 * @var string
	 */
	private $dcName;

	/**
	 * @var array|null
	 */
	private $yaml;

	/**
	 * @param string $dcName Name of the dc file, without path or extension
	 */
	public function __construct( string $dcName ) {
		$this->dcName = $dcName;
	}

	private function getYaml() {
		if(!$this->yaml) {
			$this->yaml = Yaml::parseFile(__DIR__ . '/../../../docker-compose/' . $this->dcName . '.yml');
			// TODO validate against expectations once loaded?
		}
		return $this->yaml;
	}

	private function getServices() {
		return $this->getYaml()['services'];
	}

	private function getService( string $name ) {
		return $this->getServices()[$name];
	}

	public function getServiceNames() {
		return array_keys($this->getServices());
	}

	private function getParsedEquivEnvFile( string $serviceName ) {
		$service = $this->getService( $serviceName );

		// Bail early if there are no environment values
		if(!array_key_exists('environment', $service)) {
			return '';
		}

		// Replace values from .env that need it
		$dotEnv = (new DotEnv());
		$dotEnv->updateFromDefaultAndLocal();
		$cleanedEnvData = [];
		foreach( $service['environment'] as $envLine ) {
			preg_match_all( '/\$\{([^\}]*)\}/', $envLine, $matches );
			foreach($matches[0] as $matchKey => $match) {
				$matchesEnvKey = $matches[1][$matchKey];
				$envLine = str_replace( $match, $dotEnv->getValue($matchesEnvKey), $envLine );
			}
			$cleanedEnvData[] = $envLine;
		}

		return new Parser(implode(PHP_EOL, $cleanedEnvData));
	}

	public function getVirtualHosts() {
		$hosts = [];
		foreach($this->getServiceNames() as $servicesName ) {
			$environment = $this->getParsedEquivEnvFile( $servicesName );
			$host = $environment->getContent('VIRTUAL_HOST');
			if($host) {
				$hosts[] = $host;
			}
		}
		return $hosts;
	}

}
