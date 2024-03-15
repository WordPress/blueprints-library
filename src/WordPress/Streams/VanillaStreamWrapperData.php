<?php

namespace WordPress\Streams;

class VanillaStreamWrapperData {
	public $fp;

	public function __construct( $fp ) {
		$this->fp = $fp;
	}
}
