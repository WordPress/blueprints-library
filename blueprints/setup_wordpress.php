<?php

require 'vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;
use WordPress\Blueprints\Cache\FileCache;
use WordPress\DataSource\HttpSource;
use WordPress\DataSource\ProgressEvent;
use function WordPress\Zip\zip_extract_to;

$urls = [
	'https://wordpress.org/latest.zip'                                       => 'outdir',
	'https://playground.wordpress.net/wp-cli.phar'                           => 'outdir/wordpress',
	'https://downloads.wordpress.org/plugin/woocommerce.zip'                 => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/gutenberg.zip'                   => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/akismet.zip'                     => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' => 'outdir/wordpress/wp-content/mu-plugins',
];

$fps    = [];
$client = HttpClient::create();
foreach ( $urls as $url => $target_path ) {
	$source = new HttpSource( $client, new FileCache(), $url );
	$source->events->addListener(
		ProgressEvent::class,
		function ( ProgressEvent $event ) {
			echo $event->url . ' ' . $event->downloadedBytes . '/' . $event->totalBytes . "                         \r";
		}
	);

	$fps[ $url ] = [ $source->stream(), $target_path ];
}

foreach ( $fps as $url => [$fp, $target_path] ) {
	$target_path = __DIR__ . '/' . $target_path;
	if ( str_ends_with( $url, '.zip' ) ) {
		zip_extract_to( $fp, $target_path );
	} else {
		$target_path .= '/' . basename( $url );
		$fp2         = fopen( $target_path, 'w' );
		stream_copy_to_stream( $fp, $fp2 );
		fclose( $fp2 );
	}
	fclose( $fp );
}

$db = file_get_contents( 'outdir/wordpress/wp-content/mu-plugins/sqlite-database-integration/db.copy' );
$db = str_replace(
	"'{SQLITE_IMPLEMENTATION_FOLDER_PATH}'",
	"__DIR__.'/mu-plugins/sqlite-database-integration/'",
	$db
);
$db = str_replace(
	"'{SQLITE_PLUGIN}'",
	"__DIR__.'/mu-plugins/sqlite-database-integration/load.php'",
	$db
);
file_put_contents( 'outdir/wordpress/wp-content/db.php', $db );
file_put_contents( 'outdir/wordpress/wp-content/mu-plugins/0-sqlite.php',
	'<?php require_once __DIR__ . "/sqlite-database-integration/load.php"; ' );

copy( 'outdir/wordpress/wp-config-sample.php', 'outdir/wordpress/wp-config.php' );

