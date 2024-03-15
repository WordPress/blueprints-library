<?php

namespace WordPress\Streams;

class StreamPeekerData extends VanillaStreamWrapperData {

	public function __construct( public $fp, public $onChunk = null, public $onClose = null ) {
		parent::__construct( $fp );
	}
}
