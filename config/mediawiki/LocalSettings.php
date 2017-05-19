<?php

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

## Docker stuff

$dockerRequestInfo = [];
call_user_func( function() {
	global $dockerRequestInfo;
	
	$parsedHost = parse_url( $_SERVER['HTTP_HOST'] );
	$host = $parsedHost['host'];
	
	// Defaults
	$dockerRequestInfo = [
		'webserver' => 'nginx',
		'runtime' => 'php5',
		'database' => 'mysql',
		'port' => $parsedHost['port'],
	];
	
	// Allowed Values
	$allowed = [
		'webserver' => [
			'apache',
			'nginx',
		],
		'runtime' => [
			'php5',
			'php7',
		],
		'database' => [
			'mysql',
			'mariadb',
		],
	];
	
	// Set from the domain
	foreach ( $allowed as $type => $list ) {
		foreach ( $list as $item ) {
			if ( strstr( $host, $item . '.' )  ) {
				$dockerRequestInfo[$type] = $item;
			}
		}
	}
} );

$dockerHost = $dockerRequestInfo['webserver'] . '.' . $dockerRequestInfo['runtime'] . '.' . $dockerRequestInfo['database'] . '.mw';
$dockerPort = $dockerRequestInfo['port'];

## Database settings

$wgDBserver = "mediawiki-" . $dockerRequestInfo['database'];
if ( in_array( $dockerRequestInfo['database'], [ 'mariadb', 'mysql' ] ) ) {
	$wgDBtype = "mysql";
	$wgDBprefix = "";
	$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";
}
$wgDBname = "mediawiki";
$wgDBuser = "mediawiki";
$wgDBpassword = "mwpass";
$wgDBpassword = "mwpass";

## Site settings

##$wgUsePathInfo = false;

$wgAssumeProxiesUseDefaultProtocolPorts = false; # This actually does nothing as WebRequest::detectServer() is called in DefaultSettings before this file is loaded.
$wgServer = "http://$dockerHost:$dockerPort";
$wgScriptPath = "";

$wgSitename = "mediawiki-docker $dockerHost";
$wgMetaNamespace = "Project";

$wgStatsdServer = "mediawiki-graphite-statsd";

## Dev & Debug

ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

error_reporting( -1 );
ini_set( 'display_errors', 1 );
$wgShowExceptionDetails = true;
$wgShowSQLErrors = true;
$wgDebugDumpSql  = true;
$wgShowDBErrorBacktrace = true;

$wgDebugToolbar = false;
$wgShowDebug = false;
$wgDevelopmentWarnings = false;

$wgEnableJavaScriptTest = true;

## Email

$wgEnableEmail = true;
$wgEmergencyContact = "mediawiki@$dockerHost";
$wgPasswordSender = "mediawiki@$dockerHost";
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

## Skins

$wgDefaultSkin = "vector";
wfLoadSkin( 'Vector' );

## Permissions

$wgGroupPermissions['*']['noratelimit'] = true;
$wgGroupPermissions['sysop']['editcontentmodel'] = true;

## Features

$wgRCWatchCategoryMembership = true;

