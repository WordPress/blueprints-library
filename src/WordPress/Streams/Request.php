<?php

namespace WordPress\Streams;

class Request {

	public string $url;

	/**
	 * @param string $url
	 */
	public function __construct( string $url ) {
		$this->url = $url;
	}

}