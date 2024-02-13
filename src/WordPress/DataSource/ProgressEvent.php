<?php

namespace WordPress\DataSource;

use Symfony\Contracts\EventDispatcher\Event;

class ProgressEvent extends Event {
	public function __construct(
		public string $url,
		public int $downloadedBytes,
		public int|null $totalBytes
	) {
	}
}
