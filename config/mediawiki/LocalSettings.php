<?php

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

$wgAssumeProxiesUseDefaultProtocolPorts = false;

## Docker stuff
if ( PHP_SAPI === 'cli' && !defined( 'MW_DB' ) ) {
    define( 'MW_DB', 'default' );
}

if ( defined( "MW_DB" ) ) {
    $dockerDb = MW_DB;
    $wgServer = "//$dockerDb.web.mw.localhost:80";
} elseif( array_key_exists( 'SERVER_NAME', $_SERVER ) ) {
    $dockerHostParts = explode( '.', $_SERVER['SERVER_NAME'] );
    $dockerDb = $dockerHostParts[0];
    $wgServer = WebRequest::detectServer();
} else {
    die( 'Unable to decide which site is being used.' );
}

## Database settings
$wgDBname = $dockerDb;

// Decide on which services are available?
$mwddServices = [
	// Configure a replica DB if it is running and we are not in unit tests
	'db-replica' => gethostbyname('db-replica') !== 'db-replica' && !defined( 'MW_PHPUNIT_TEST' ),
	'redis' => gethostbyname('redis') !== 'redis',
	'graphite-statsd' => gethostbyname('graphite-statsd') !== 'graphite-statsd',
];

$wgDBservers = [
	[
		'host' => "db-master",
		'dbname' => $dockerDb,
		'user' => 'root',
		'password' => 'toor',
		'type' => "mysql",
		'flags' => DBO_DEFAULT,
		'load' => $mwddServices['db-replica'] ? 0 : 1,
	],
];
if($mwddServices['db-replica'] ) {
	$wgDBservers[] = [
		'host' => "db-replica",
		'dbname' => $dockerDb,
		'user' => 'root',
		'password' => 'toor',
		'type' => "mysql",
		'flags' => DBO_DEFAULT,
		# Avoid switching to readonly too early (for example during update.php)
		'max lag' => 60,
		'load' => 1,
	];
}

// If a redis service is running, then configure an object cache (but don't use it)
if(gethostbyname('redis') !== 'redis') {
	$wgObjectCaches['redis'] = [
		'class' => 'RedisBagOStuff',
		'servers' => [ 'redis:6379' ],
	];
}

// Configure a statsd server if it is running
if(gethostbyname('graphite-statsd') !== 'graphite-statsd') {
	$wgStatsdServer = "graphite-statsd";
}

require_once __DIR__ . '/MwddSpecialPage.php';
$wgSpecialPages['Mwdd'] = MwddSpecial::class;
$wgExtensionMessagesFiles['Mwdd'] = __DIR__ . '/special-aliases.php';

$wgShowHostnames = true;

// mysql only stuff (would need to change for sqlite)
$wgDBprefix = "";
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

## Site settings
$wgScriptPath = "/mediawiki";

$wgSitename = "docker-$dockerDb";
$wgMetaNamespace = "Project";
$wgFavicon = "{$wgScriptPath}/.docker/favicon.ico";

$wgUploadDirectory = "{$IP}/images/docker/{$dockerDb}";
$wgUploadPath = "{$wgScriptPath}/images/docker/{$dockerDb}";

$wgTmpDirectory = "{$wgUploadDirectory}/tmp";
$wgCacheDirectory = "{$wgUploadDirectory}/cache";

## Dev & Debug

$dockerLogDirectory = "/var/log/mediawiki";
$wgDebugLogFile = "$dockerLogDirectory/debug.log";

ini_set( 'xdebug.var_display_max_depth', -1 );
ini_set( 'xdebug.var_display_max_children', -1 );
ini_set( 'xdebug.var_display_max_data', -1 );

error_reporting( -1 );
ini_set( 'display_errors', 1 );
$wgShowExceptionDetails = true;
$wgShowSQLErrors = true;
$wgDebugDumpSql  = true;
$wgShowDBErrorBacktrace = true;

$wgDebugToolbar = false;
$wgShowDebug = false;
$wgDevelopmentWarnings = true;

$wgEnableJavaScriptTest = true;

## Email

$wgEnableEmail = true;
$wgEmergencyContact = "mediawiki@$dockerDb";
$wgPasswordSender = "mediawiki@$dockerDb";
$wgEnableUserEmail = true;
$wgEmailAuthentication = true;

## Notifications

$wgEnotifUserTalk = false;
$wgEnotifWatchlist = false;

## Files

$wgEnableUploads = true;
$wgAllowCopyUploads = true;
$wgUseInstantCommons = false;

$wgFileExtensions = array_merge( $wgFileExtensions,
	array( 'doc', 'xls', 'mpp', 'pdf', 'ppt', 'xlsx', 'jpg',
		'tiff', 'odt', 'odg', 'ods', 'odp', 'svg'
	)
);
$wgFileExtensions[] = 'djvu';

## Locale

$wgShellLocale = "en_US.utf8";
$wgLanguageCode = "en";

## Keys

$wgAuthenticationTokenVersion = "1";
$wgUpgradeKey = "t8qu09t9uw09ti09itq092t3j";
$wgSecretKey = "a5dca55190e1c3927e098c317dd74e85c7eced36f959275114773b188fbabdbc";

## Permissions

$wgGroupPermissions['*']['noratelimit'] = true;
$wgGroupPermissions['sysop']['editcontentmodel'] = true;

## Features

$wgRCWatchCategoryMembership = true;

## PHP Location
$wgPhpCli = '/usr/local/bin/php';
