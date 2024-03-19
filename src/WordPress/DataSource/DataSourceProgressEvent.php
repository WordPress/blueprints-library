<?php

namespace WordPress\DataSource;

use Symfony\Component\EventDispatcher\Event;

class DataSourceProgressEvent extends Event {
	/**
	 * @var string
	 */
	public $url;
	/**
	 * @var int
	 */
	public $downloadedBytes;
	/**
	 * @var int|null
	 */
	public $totalBytes;
	/**
	 * @param int|null $totalBytes
	 */
	public function __construct( string $url, int $downloadedBytes, $totalBytes ) {
		$this->url             = $url;
		$this->downloadedBytes = $downloadedBytes;
		$this->totalBytes      = $totalBytes;
	}
}
