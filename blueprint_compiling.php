<?php

use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\BlueprintBuilder;
use function WordPress\Blueprints\run_blueprint;

if ( getenv( 'USE_PHAR' ) ) {
	require __DIR__ . '/dist/blueprints.phar';
} else {
	require 'vendor/autoload.php';
}

$blueprint = BlueprintBuilder::create()
	->withWordPressVersion( 'https://wordpress.org/latest.zip' )
	->withSiteOptions( [
		'blogname' => 'My Playground Blog',
	] )
	->withWpConfigConstants( [
		'WP_DEBUG'         => true,
		'WP_DEBUG_LOG'     => true,
		'WP_DEBUG_DISPLAY' => true,
		'WP_CACHE'         => true,
	] )
	->withPlugins( [
		// Required for withContent():
		'https://downloads.wordpress.org/plugin/wordpress-importer.zip',
		'https://downloads.wordpress.org/plugin/hello-dolly.zip',
		'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip',
	] )
	->withTheme( 'https://downloads.wordpress.org/theme/pendant.zip' )
	->withContent( 'https://raw.githubusercontent.com/WordPress/theme-test-data/master/themeunittestdata.wordpress.xml' )
	->withSiteUrl( 'http://localhost:8081' )
	->andRunSQL( <<<'SQL'
		CREATE TABLE `tmp_table` ( id INT );
		INSERT INTO `tmp_table` VALUES (1);
		INSERT INTO `tmp_table` VALUES (2);
		SQL
	)
	->withFile( 'wordpress.txt', 'Data' )
	->toBlueprint();


$results = run_blueprint( $blueprint, ContainerBuilder::ENVIRONMENT_NATIVE, __DIR__ . '/new-wp' );

var_dump( $results );
