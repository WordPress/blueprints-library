<?php

namespace WordPress\AsyncHttp;

class RequestInfo {
	const STATE_ENQUEUED  = 'STATE_ENQUEUED';
	const STATE_STREAMING = 'STATE_STREAMING';
	const STATE_FINISHED  = 'STATE_FINISHED';
	public $state         = self::STATE_ENQUEUED;
	public $stream;
	public $buffer = '';

	/**
	 * @param $stream
	 */
	public function __construct( $stream ) {
		$this->stream = $stream;
	}

	public function is_finished() {
		return $this->state === self::STATE_FINISHED;
	}
}
