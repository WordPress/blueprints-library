<?php

namespace WordPress\Streams;

class PollManyStreamData extends VanillaStreamWrapperData {
	public AsyncHttpClient $group;

	public function __construct( $fp, AsyncHttpClient $group ) {
		parent::__construct( $fp );
		$this->group = $group;
	}
}
