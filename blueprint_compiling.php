<?php

use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\BlueprintComposer;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

require 'vendor/autoload.php';

$composer = BlueprintComposer::create()
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
	->withFile( 'wordpress.txt', 'Data' );

$c = ( new ContainerBuilder() )->build(
	new NativePHPRuntime(
		__DIR__ . '/new-wp'
	)
);

$results = $c['blueprint.engine']->runBlueprint( $composer );

var_dump( $results );
