<?php

namespace WordPress\Blueprints\Progress;

use Symfony\Component\EventDispatcher\Event;

class ProgressCaptionEvent extends Event {
	/**
	 * @var string
	 */
	public $caption;
	public function __construct( string $caption ) {
		$this->caption = $caption;
	}
}
