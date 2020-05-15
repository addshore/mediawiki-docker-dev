<?php

namespace Addshore\Mwdd\DockerCompose;

class Statsd {

	public const SRV_GRAPHITE_STATSD = 'graphite-statsd';

	public const SERVICES = [
		self::SRV_GRAPHITE_STATSD,
	];

}
