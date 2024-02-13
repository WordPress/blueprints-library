<?php

namespace WordPress\Streams;

class StreamPeekerContext {

	public function __construct( public $fp, public $onChunk = null, public $onClose = null ) {
	}
}
