<?php

namespace WordPress\DataSource;

use Symfony\Component\EventDispatcher\EventDispatcher;

class FileSource implements DataSourceInterface {

	public function __construct(
		protected string $path
	) {
	}

	public function stream() {
		return fopen( $this->path, 'r' );
	}
}
