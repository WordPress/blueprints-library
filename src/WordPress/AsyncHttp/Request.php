<?php

namespace WordPress\AsyncHttp;

class Request {

	public $url;

	/**
	 * @param string $url
	 */
	public function __construct( string $url ) {
		$this->url = $url;
	}

}
