<?php

use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintBuilder;
use WordPress\Blueprints\Model\Builder\BlueprintPreferredVersionsBuilder;
use WordPress\Blueprints\Model\Builder\InlineResourceBuilder;
use WordPress\Blueprints\Model\Builder\WriteFileStepBuilder;
use WordPress\Blueprints\Runtime\NativePHPRuntime;

$builder = new BlueprintBuilder();
$builder
	->setPreferredVersions(
		( new BlueprintPreferredVersionsBuilder() )
			->setPhp( '7.4' )
			->setWp( '5.3' )
	)
	->setPlugins( [
		'akismet',
		'hello-dolly',
	] )
	->setPhpExtensionBundles( [
		'kitchen-sink',
	] )
	->setLandingPage( "/wp-admin" )
	->setSteps( [
//		( new \WordPress\Blueprints\Model\Builder\RunWordPressInstallerStepBuilder() )->setOptions(
//			( new WordPressInstallationOptionsBuilder() )
//				->setAdminUsername( 'admin' )
//				->setAdminPassword( 'password' )
//		),
		( new \WordPress\Blueprints\Model\Builder\RunPHPStepBuilder() )
			->setCode( '<?php echo "A"; ' ),
		( new \WordPress\Blueprints\Model\Builder\SetSiteOptionsStepBuilder() )
			->setOptions( (object) [
				'blogname' => 'My Playground Blog',
			] ),
		( new \WordPress\Blueprints\Model\Builder\DefineWpConfigConstsStepBuilder() )
			->setConsts( (object) [
				'WP_DEBUG'         => true,
				'WP_DEBUG_LOG'     => true,
				'WP_DEBUG_DISPLAY' => true,
				'WP_CACHE'         => true,
			] ),
		( new \WordPress\Blueprints\Model\Builder\ActivatePluginStepBuilder() )
			->setSlug( 'hello-dolly' ),
//		( new \WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder() )
//			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/hello-dolly.zip' )
//			->setOptions( ( new InstallPluginOptionsBuilder() )->setActivate( true ) ),
//		( new \WordPress\Blueprints\Model\Builder\InstallThemeStepBuilder() )
//			->setThemeZipFile( 'https://downloads.wordpress.org/theme/pendant.zip' )
//			->setOptions( ( new \WordPress\Blueprints\Model\Builder\InstallThemeStepOptionsBuilder() )->setActivate( true ) ),
//		( new \WordPress\Blueprints\Model\Builder\ImportFileStepBuilder() )
//			->setFile( 'https://raw.githubusercontent.com/WordPress/theme-test-data/master/themeunittestdata.wordpress.xml' ),
//		( new \WordPress\Blueprints\Model\Builder\InstallPluginStepBuilder() )
//			->setPluginZipFile( 'https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip' )
//			->setOptions( ( new InstallPluginOptionsBuilder() )->setActivate( true ) ),
		( new \WordPress\Blueprints\Model\Builder\DefineSiteUrlStepBuilder() )
			->setSiteUrl( 'http://localhost:8080' ),
//		( new \WordPress\Blueprints\Model\Builder\RunSQLStepBuilder() )
//			->setSql( ( new LiteralReferenceBuilder() )->setContents(
//				<<<'SQL'
//CREATE TABLE `tmp_table` ( id INT );
//INSERT INTO `tmp_table` VALUES (1);
//INSERT INTO `tmp_table` VALUES (2);
//SQL
//
//			) ),
		( new WriteFileStepBuilder() )
			->setContinueOnError( true )
			->setPath( 'wordpress.txt' )
			->setData( ( new InlineResourceBuilder() )->setContents( "Data" ) ),
//		( ( new UnzipStepBuilder() )
//			->setZipFile(
//				'https://wordpress.org/latest.zip'
////				( new UrlReferenceBuilder() )->setUrl( 'https://wordpress.org/latest.zip' )
//			) )
//			->setExtractToPath( __DIR__ . '/outdir2' ),
//		( new WriteFileStepBuilder() )
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
