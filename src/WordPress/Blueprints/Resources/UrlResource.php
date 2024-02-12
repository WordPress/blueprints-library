<?php

namespace WordPress\Blueprints\Resources;

class UrlResource extends StreamResource {
	public function __construct(
		protected $url
	) {
		parent::__construct( fopen( $url, 'r' ) );
	}
}
