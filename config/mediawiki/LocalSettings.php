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
$dockerMasterDb = [
	'host' => "db-master",
	'dbname' => $dockerDb,
	'user' => 'root',
	'password' => 'toor',
	'type' => "mysql",
	'flags' => DBO_DEFAULT,
	'load' => 0,
];
$dockerSlaveDb = [
	'host' => "db-slave",
	'dbname' => $dockerDb,
	'user' => 'root',
	'password' => 'toor',
	'type' => "mysql",
	'flags' => DBO_DEFAULT,
	# Avoid switching to readonly too early (for example during update.php)
	'max lag' => 60,
	'load' => 1,
];
// Unit tests fail when run with replication, due to not having the temporary tables.
// So for unit tests just run with the master.
if ( !defined( 'MW_PHPUNIT_TEST' ) ) {
	$wgDBservers = [ $dockerMasterDb, $dockerSlaveDb ];
} else {
	$wgDBserver = $dockerMasterDb['host'];
	$wgDBuser = $dockerMasterDb['user'];
	$wgDBpassword = $dockerMasterDb['password'];
	$wgDBtype = $dockerMasterDb['type'];
}

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

$wgStatsdServer = "graphite-statsd";

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
