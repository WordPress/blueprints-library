<?php

use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\BlueprintPreferredVersions;
use WordPress\Blueprints\Model\DataClass\InlineResource;
use WordPress\Blueprints\Model\DataClass\WriteFileStep;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

require 'vendor/autoload.php';

$builder = new Blueprint();
$builder
	->setWpVersion( 'https://wordpress.org/latest.zip' )
	->setPlugins( [
		'https://downloads.wordpress.org/plugin/hello-dolly.zip',
		'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip',
	] )
	->setLandingPage( "/wp-admin" )
	->setSteps( [
//		( new \WordPress\Blueprints\Model\DataClass\RunWordPressInstallerStep() )->setOptions(
//			( new WordPressInstallationOptions() )
//				->setAdminUsername( 'admin' )
//				->setAdminPassword( 'password' )
//		),
		( new \WordPress\Blueprints\Model\DataClass\RunPHPStep() )
			->setCode( '<?php echo "A"; ' ),
		( new \WordPress\Blueprints\Model\DataClass\SetSiteOptionsStep() )
			->setOptions( [
				'blogname' => 'My Playground Blog',
			] ),
		( new \WordPress\Blueprints\Model\DataClass\DefineWpConfigConstsStep() )
			->setConsts( [
				'WP_DEBUG'         => true,
				'WP_DEBUG_LOG'     => true,
				'WP_DEBUG_DISPLAY' => true,
				'WP_CACHE'         => true,
			] ),
//		( new \WordPress\Blueprints\Model\DataClass\ActivatePluginStep() )
//			->setSlug( 'hello-dolly' ),
		( new \WordPress\Blueprints\Model\DataClass\InstallPluginStep() )
			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/wordpress-importer.zip', ),
//			->setOptions( ( new InstallPluginOptions() )->setActivate( true ) ),
//		( new \WordPress\Blueprints\Model\DataClass\InstallThemeStep() )
//			->setThemeZipFile( 'https://downloads.wordpress.org/theme/pendant.zip' )
//			->setOptions( ( new \WordPress\Blueprints\Model\DataClass\InstallThemeStepOptions() )->setActivate( true ) ),
		( new \WordPress\Blueprints\Model\DataClass\ImportFileStep() )
			->setFile( 'https://raw.githubusercontent.com/WordPress/theme-test-data/master/themeunittestdata.wordpress.xml' ),
//		( new \WordPress\Blueprints\Model\DataClass\InstallPluginStep() )
//			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip' )
//			->setOptions( ( new InstallPluginOptions() )->setActivate( true ) ),
		( new \WordPress\Blueprints\Model\DataClass\DefineSiteUrlStep() )
			->setSiteUrl( 'http://localhost:8080' ),
		( new \WordPress\Blueprints\Model\DataClass\RunSQLStep() )
			->setSql( ( new InlineResource() )->setContents(
				<<<'SQL'
CREATE TABLE `tmp_table` ( id INT );
INSERT INTO `tmp_table` VALUES (1);
INSERT INTO `tmp_table` VALUES (2);
SQL

			) ),
		( new WriteFileStep() )
			->setContinueOnError( true )
			->setPath( 'wordpress.txt' )
			->setData( ( new InlineResource() )->setContents( "Data" ) ),
//		( ( new UnzipStep() )
//			->setZipFile(
//				'https://wordpress.org/latest.zip'
////				( new UrlReference() )->setUrl( 'https://wordpress.org/latest.zip' )
//			) )
//			->setExtractToPath( __DIR__ . '/outdir2' ),
//		( new WriteFileStep() )
//			->setPath( __DIR__ . '/outdir2/test.zip' )
//			->setData( 'https://wordpress.org/latest.zip' ),
	] );

$c = ( new ContainerBuilder() )->build(
	new NativePHPRuntime(
		__DIR__ . '/outdir/wordpress'
	)
);

$results = $c['blueprint.engine']->runBlueprint( $builder );

var_dump( $results );
