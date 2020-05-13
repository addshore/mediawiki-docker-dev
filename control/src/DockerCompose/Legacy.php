<?php

namespace Addshore\Mwdd\DockerCompose;

/**
 * Fake docker-compose file (doesnt exist) for the legacy services
 */
class Legacy {

	public const SRV_MEDIAWIKI = Base::SRV_MEDIAWIKI;
	public const SRV_DPS = Base::SRV_DPS;
	public const SRV_DB_MASTER = Base::SRV_DB_MASTER;
	public const SRV_NGINX_PROXY = Base::SRV_NGINX_PROXY;

	public const SRV_DB_REPLICA = DbReplica::SRV_DB_REPLICA;
	public const SRV_DB_CONFIGURE = DbReplica::SRV_DB_CONFIGURE;

	public const SRV_REDIS = Redis::SRV_REDIS;

	public const SRV_PHPMYADMIN = PhpMyAdmin::SRV_PHPMYADMIN;

	public const SERVICES = [
		self::SRV_DPS,
		self::SRV_DB_MASTER,
		self::SRV_DB_REPLICA,
		self::SRV_DB_CONFIGURE,
		self::SRV_PHPMYADMIN,
		self::SRV_MEDIAWIKI,
		self::SRV_NGINX_PROXY,
		self::SRV_REDIS,
	];

}
