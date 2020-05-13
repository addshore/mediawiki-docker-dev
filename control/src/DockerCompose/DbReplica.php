<?php

namespace Addshore\Mwdd\DockerCompose;

class DbReplica {

	public const SRV_DB_REPLICA = 'db-replica';
	public const SRV_DB_CONFIGURE = 'db-configure';

	public const SERVICES = [
		self::SRV_DB_REPLICA,
		self::SRV_DB_CONFIGURE,
	];

}
