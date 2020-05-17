<?php

namespace Addshore\Mwdd\DockerCompose;

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

	public function getServiceNames() {
		return array_keys($this->getServices());
	}

	public function getVirtualHosts() {
		$hosts = [];
		foreach($this->getServices() as $servicesName => $serviceData ) {
			if(!array_key_exists('environment', $serviceData)) {
				return [];
			}
			$environment = (new Parser(implode(PHP_EOL, $serviceData['environment'])));
			$host = $environment->getContent('VIRTUAL_HOST');
			if($host) {
				$hosts[] = $host;
			}
		}
		return $hosts;
	}

}
