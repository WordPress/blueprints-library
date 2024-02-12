<?php

namespace WordPress\Streams;

class StreamPeekerData {

	public function __construct( public $fp, public $onChunk = null, public $onClose = null ) {
	}
}
