<?php

namespace WordPress\Streams;

class StreamPeekerData {

	public function __construct( public $fp, public $callback, public $on_close ) {
	}
}
