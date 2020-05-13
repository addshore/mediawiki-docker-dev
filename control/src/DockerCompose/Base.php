<?php

namespace Addshore\Mwdd\DockerCompose;

class Base {

	public const SRV_WEB = 'web';
	public const SRV_PHPMYADMIN = 'phpmyadmin';
	public const SRV_DPS = 'dps';
	public const SRV_DB_MASTER = 'db-master';
	public const SRV_DB_SLAVE = 'db-slave';
	public const SRV_DB_CONFIGURE = 'db-configure';
	public const SRV_NGINX_PROXY = 'nginx-proxy';
	public const SRV_REDIS = 'redis';

	public const SERVICES = [
		self::SRV_DPS,
		self::SRV_DB_MASTER,
		self::SRV_DB_SLAVE,
		self::SRV_DB_CONFIGURE,
		self::SRV_PHPMYADMIN,
		self::SRV_WEB,
		self::SRV_NGINX_PROXY,
		self::SRV_REDIS,
	];

}
