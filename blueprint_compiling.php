<?php

use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\DefineSiteUrlStepBuilder;
use WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder;
use WordPress\Blueprints\Model\Builder\ImportFileStepBuilder;
use WordPress\Blueprints\Model\Builder\InitializeWordPressStepBuilder;
use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallSqliteIntegrationStepBuilder;
use WordPress\Blueprints\Model\Builder\InstallThemeStepBuilder;
use WordPress\Blueprints\Model\Builder\RunSQLStepBuilder;
use WordPress\Blueprints\Model\Builder\RunWordPressInstallerStepBuilder;
use WordPress\Blueprints\Model\Builder\SetSiteOptionsStepBuilder;
use WordPress\Blueprints\Model\Builder\UrlResourceBuilder;
use WordPress\Blueprints\Model\Builder\WordPressInstallationOptionsBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

require 'vendor/autoload.php';

$builder = new BlueprintBuilder();
$builder
	->setWpVersion( '6.4' )
	->setPlugins( [
		'akismet',
		'hello-dolly',
	] )
	->setSteps( [
		( ( new InitializeWordPressStepBuilder() )
			->setWordPressZip( 'https://wordpress.org/latest.zip' ) ),
		( ( new InstallSqliteIntegrationStepBuilder() )
			->setSqlitePluginZip(
				'https://downloads.wordpress.org/plugin/sqlite-database-integration.zip'
			) ),
		( new WriteFileStepBuilder() )
			->setContinueOnError( true )
			->setPath( 'wp-cli.phar' )
			->setData( ( new UrlResourceBuilder() )->setUrl( 'https://playground.wordpress.net/wp-cli.phar' ) ),
		( new RunWordPressInstallerStepBuilder() )->setOptions(
			new WordPressInstallationOptionsBuilder()
		),
		( new SetSiteOptionsStepBuilder() )
			->setOptions( (object) [
				'blogname' => 'My Playground Blog',
			] ),
		( new DefineWpConfigConstsStepBuilder() )
			->setConsts( (object) [
				'WP_DEBUG'         => true,
				'WP_DEBUG_LOG'     => true,
				'WP_DEBUG_DISPLAY' => true,
				'WP_CACHE'         => true,
			] ),
		( new InstallPluginStepBuilder() )
			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/wordpress-importer.zip' ),
		( new InstallPluginStepBuilder() )
			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/hello-dolly.zip' ),
		( new InstallThemeStepBuilder() )
			->setThemeZipFile( 'https://downloads.wordpress.org/theme/pendant.zip' ),
		( new ImportFileStepBuilder() )
			->setFile( 'https://raw.githubusercontent.com/WordPress/theme-test-data/master/themeunittestdata.wordpress.xml' ),
		( new InstallPluginStepBuilder() )
			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip' ),
		( new DefineSiteUrlStepBuilder() )
			->setSiteUrl( 'http://localhost:8081' ),
		( new RunSQLStepBuilder() )
			->setSql( <<<'SQL'
CREATE TABLE `tmp_table` ( id INT );
INSERT INTO `tmp_table` VALUES (1);
INSERT INTO `tmp_table` VALUES (2);
SQL

			),
		( new WriteFileStepBuilder() )
			->setPath( 'wordpress.txt' )
			->setData( ( new InlineResourceBuilder() )->setContents( "Data" ) ),
	] );

$c = ( new ContainerBuilder() )->build(
	new NativePHPRuntime(
		__DIR__ . '/new-wp'
	)
);

$results = $c['blueprint.engine']->runBlueprint( $builder );

var_dump( $results );
