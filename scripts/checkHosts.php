<?php

// TODO make this not require PHP... (write it in bash...)

// Get the content of our hosts file
$windowsLocation = 'C:\Windows\System32\drivers\etc\hosts';
$nixLocation = '/etc/hosts';

if ( file_exists( $windowsLocation ) ) {
    $location = $windowsLocation;
} elseif( file_exists( $nixLocation ) ) {
    $location = $nixLocation;
} else {
    echo "Can't find your hosts file to check!" . PHP_EOL;
}
$localHostsFile = file_get_contents( $location );
$localHostsFile = file_get_contents( $windowsLocation );

// And the content that we want
$dockerHosts = file_get_contents( __DIR__ . '/../config/local/hosts' );

// Do we need to update?
$hasStart = strstr( $localHostsFile, 'mw docker start' );
if ( !strstr( $localHostsFile, $dockerHosts ) ) {
    if( $hasStart ) {
        echo "Your hosts file has fallen out of date!" . PHP_EOL;
        echo "Please replace the mw docker section with the following:" . PHP_EOL;
    } else {
        echo "You need to add the following entries to your hosts file to get the most out of this docker stuff!" . PHP_EOL;
        echo "Please add the following to your hosts file:" . PHP_EOL;
    }
    echo "#------------------------------------------------------------------------------" . PHP_EOL;
    echo $dockerHosts . PHP_EOL;
    echo "#------------------------------------------------------------------------------" . PHP_EOL;
    echo "You can find your hosts file at: $location" . PHP_EOL;
} else {
    echo "Hosts file all up to date!" . PHP_EOL;
    exit();
}
exit(1);