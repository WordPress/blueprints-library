<?php

require 'vendor/autoload.php';

use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\StepHandler\Implementation\UnzipStepHandler;
use WordPress\Blueprints\StepHandler\Unzip\UnzipStepInput;
use WordPress\Blueprints\StepHandler\WriteFile\WriteFileStepInput;
use WordPress\Blueprints\StepHandler\WriteFileHandler;

$urls = [
	'https://wordpress.org/latest.zip'                                       => 'outdir',
	'https://playground.wordpress.net/wp-cli.phar'                           => 'outdir/wordpress/wp-cli.phar',
	'https://downloads.wordpress.org/plugin/woocommerce.zip'                 => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/gutenberg.zip'                   => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/akismet.zip'                     => 'outdir/wordpress/wp-content/plugins',
	'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip' => 'outdir/wordpress/wp-content/mu-plugins',
];

$container = ContainerBuilder::build( 'native' );

$steps = [];
foreach ( $urls as $url => $target_path ) {
	$fp = $container['data_source.url']->stream( $url );
	if ( str_ends_with( $url, '.zip' ) ) {
		$steps[] = function () use ( $fp, $target_path ) {
			$step = new UnzipStepHandler();
			$step->execute(
				new UnzipStepInput( $fp, $target_path )
			);
			fclose( $fp );
		};
	} else {
		$steps[] = function () use ( $fp, $target_path ) {
			$step = new WriteFileHandler();
			$step->execute(
				new WriteFileStepInput( $fp, $target_path )
			);
			fclose( $fp );
		};
	}
}

$steps[] = function () {
	$db = file_get_contents( 'wordpress/wp-content/mu-plugins/sqlite-database-integration/db.copy' );
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
	file_put_contents( 'wordpress/wp-content/db.php', $db );
	file_put_contents( 'wordpress/wp-content/mu-plugins/0-sqlite.php',
		'<?php require_once __DIR__ . "/sqlite-database-integration/load.php"; ' );

	copy( 'wordpress/wp-config-sample.php', 'wordpress/wp-config.php' );
};

foreach ( $steps as $step ) {
	$step();
}
