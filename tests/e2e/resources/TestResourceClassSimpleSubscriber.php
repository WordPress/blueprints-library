<?php

namespace e2e\resources;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WordPress\Blueprints\Progress\DoneEvent;
use WordPress\Blueprints\Progress\ProgressEvent;

class TestResourceClassSimpleSubscriber implements EventSubscriberInterface {
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
}
