<?php

namespace Addshore\Mwdd\DockerCompose;

class Base {

	public const SRV_MEDIAWIKI = 'mediawiki';
	public const SRV_DPS = 'dps';
	public const SRV_DB_MASTER = 'db-master';
	public const SRV_DB_CONFIGURE = 'db-configure';
	public const SRV_NGINX_PROXY = 'nginx-proxy';

	public const SERVICES = [
		self::SRV_DPS,
		self::SRV_DB_MASTER,
		self::SRV_DB_CONFIGURE,
		self::SRV_NGINX_PROXY,
		self::SRV_MEDIAWIKI,
	];

}
