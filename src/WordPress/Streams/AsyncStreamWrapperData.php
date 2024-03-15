<?php

namespace WordPress\Streams;

class AsyncStreamWrapperData extends VanillaStreamWrapperData {
	public StreamPollingGroup $group;

	public function __construct( $fp, StreamPollingGroup $group ) {
		parent::__construct( $fp );
		$this->group = $group;
	}
}
