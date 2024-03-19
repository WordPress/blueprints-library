<?php

namespace WordPress\Blueprints\Progress;

use Symfony\Component\EventDispatcher\Event;

/**
 * Custom event providing progress details.
 */
class ProgressEvent extends Event {
	/**
	 * The progress percentage as a number between 0 and 100.
	 *
	 * @var float $progress
	 */
	public $progress;

	/**
	 * The caption to display during progress, a string.
	 *
	 * @var ?string $caption
	 */
	public $caption;

	public function __construct(
		float $progress,
		string $caption
	) {
		$this->caption  = $caption;
		$this->progress = $progress;
	}
}
