<?php

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Model\BlueprintBuilder;
use WordPress\Blueprints\Progress\DoneEvent;
use WordPress\Blueprints\Progress\ProgressEvent;
use function WordPress\Blueprints\run_blueprint;

if ( getenv( 'USE_PHAR' ) ) {
	require __DIR__ . '/../dist/blueprints.phar';
} else {
	require __DIR__ . '/../vendor/autoload.php';
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
//	->withContent( 'https://raw.githubusercontent.com/WordPress/theme-test-data/master/themeunittestdata.wordpress.xml' )
	->withSiteUrl( 'http://localhost:8081' )
	->andRunSQL( <<<'SQL'
CREATE TABLE `tmp_table` ( id INT );
INSERT INTO `tmp_table` VALUES (1);
INSERT INTO `tmp_table` VALUES (2);
SQL
	)
	->withFile( 'wordpress.txt', 'Data' )
	->toBlueprint();

$subscriber = new class implements EventSubscriberInterface {
	public static function getSubscribedEvents() {
		return [
			ProgressEvent::class => 'onProgress',
			DoneEvent::class     => 'onDone',
		];
	}

	protected $progress_bar;

	public function __construct() {
	}

	public function onProgress( ProgressEvent $event ) {
		echo $event->caption . " – " . $event->progress . "%\n";
	}

	public function onDone( DoneEvent $event ) {
	}
};

$results = run_blueprint(
	$blueprint,
	[
		'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
		'documentRoot'       => __DIR__ . '/new-wp',
		'progressSubscriber' => $subscriber,
	]
);
