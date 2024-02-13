<?php

require 'vendor/autoload.php';

use WordPress\Blueprints\ContainerBuilder;
use function WordPress\Zip\zip_extract_to;

$urls = [
	'https://wordpress.org/latest.zip'                                       => 'outdir',
	'https://playground.wordpress.net/wp-cli.phar'                           => 'outdir/wordpress',
	'https://downloads.wordpress.org/plugin/woocommerce.zip'                 => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/gutenberg.zip'                   => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/akismet.zip'                     => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' => 'outdir/wordpress/wp-content/mu-plugins',
];

$container = ContainerBuilder::build( 'native' );

$fps = [];
foreach ( $urls as $url => $target_path ) {
	$fps[ $url ] = [
		$container['data_source.url']->stream( $url ),
		$target_path,
	];
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

