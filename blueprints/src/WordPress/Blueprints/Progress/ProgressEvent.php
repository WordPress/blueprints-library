<?php

namespace WordPress\Blueprints\Progress;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Custom event providing progress details.
 */
class ProgressEvent extends Event {
	public function __construct(
		/** The progress percentage as a number between 0 and 100. */
		public float $progress,
		/** The caption to display during progress, a string. */
		public ?string $caption,
	) {
	}
}
