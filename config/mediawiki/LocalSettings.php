<?php

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}

## Docker stuff

$dockerParsedHost = parse_url( $_SERVER['HTTP_HOST'] );
$dockerPort = $dockerParsedHost['port'];
$dockerHost = $dockerParsedHost['host'];
$dockerHostParts = explode( '.', $dockerParsedHost['host'] );
$dockerSiteName = $dockerHostParts[0];

## Database settings
$wgDBserver = "db";
$wgDBname = $dockerSiteName;
$wgDBuser = "root";
$wgDBpassword = "toor";

// mysql only stuff (would need to change for sqlite)
$wgDBtype = "mysql";
$wgDBprefix = "";
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

## Site settings

## $wgUsePathInfo = false;

$wgAssumeProxiesUseDefaultProtocolPorts = false; # This actually does nothing as WebRequest::detectServer() is called in DefaultSettings before this file is loaded.
$wgServer = "http://$dockerHost:$dockerPort";
$wgScriptPath = "";

$wgSitename = "docker-$dockerSiteName";
$wgMetaNamespace = "Project";

$wgStatsdServer = "graphite-statsd";

## Dev & Debug

$wgDebugLogFile = "/var/log/debug.log";

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

