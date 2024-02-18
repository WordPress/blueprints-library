<?php

namespace WordPress\Blueprints\Progress;

class ProgressCaptionEvent extends \Symfony\Contracts\EventDispatcher\Event {
	public function __construct(
		public string $caption
	) {
	}
}
