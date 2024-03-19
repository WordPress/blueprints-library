<?php

namespace WordPress\Streams;

class StreamPeekerData extends VanillaStreamWrapperData {

	public $fp;
	public $onChunk = null;
	public $onClose = null;
	public function __construct( $fp, $onChunk = null, $onClose = null ) {
		$this->fp      = $fp;
		$this->onChunk = $onChunk;
		$this->onClose = $onClose;
		parent::__construct( $fp );
	}
}
