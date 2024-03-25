<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WordPress\Blueprints\ContainerBuilder;
use WordPress\Blueprints\Progress\DoneEvent;
use WordPress\Blueprints\Progress\ProgressEvent;
use function WordPress\Blueprints\run_blueprint;

if ( getenv( 'USE_PHAR' ) ) {
//	require __DIR__ . './../dist/blueprints.phar';
} else {
	require './../vendor/autoload.php';
}

$blueprint = '{"WordPressVersion":"https://wordpress.org/latest.zip"}';

$subscriber = new class() implements EventSubscriberInterface {
	public static function getSubscribedEvents() {
		return [
			ProgressEvent::class => 'onProgress',
			DoneEvent::class => 'onDone',
		];
	}

	protected $progress_bar;

	public function __construct() {}

	public function onProgress( ProgressEvent $event ) {}

	public function onDone( DoneEvent $event ) {}
};

$results = run_blueprint(
	$blueprint,
	array(
		'environment'        => ContainerBuilder::ENVIRONMENT_NATIVE,
		'documentRoot'       => __DIR__ . '/new-wp',
		'progressSubscriber' => $subscriber,
	)
);
