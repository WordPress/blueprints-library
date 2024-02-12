<?php

namespace blueprints\src\WordPress\Blueprints\Resources;

class UrlResourcePHP extends PHPStreamResource {
	public function __construct(
		protected $url
	) {
		parent::__construct( fopen( $url, 'r' ) );
	}
}
