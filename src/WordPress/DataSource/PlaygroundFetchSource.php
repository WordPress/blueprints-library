<?php

namespace WordPress\DataSource;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\Response\StreamWrapper;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WordPress\Streams\StreamPeeker;
use WordPress\Streams\StreamPeekerContext;

class PlaygroundFetchSource extends BaseDataSource {

	public $proc_handles = [];

	public function stream( $resourceIdentifier ) {
		$url = $resourceIdentifier;
		$proc_handle = proc_open(
			[ 'fetch', $url, ],
			[ 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ],
			$pipes
		);
		// This prevents the process handle from getting garbage collected and
		// breaking the stdout pipe. However, how the program never terminates.
		// Presumably we need to peek() on the resource handle and close the
		// process handle when it's done.
		// Without this line, we get the following error:
		// PHP Fatal error:  Uncaught TypeError: stream_copy_to_stream(): supplied resource is not a valid stream resource i
		// var_dump()–ing first says
		//    resource(457) of type (stream)
		// but then it says
		//    resource(457) of type (Unknown)
		$this->proc_handles[] = $proc_handle;

		return $pipes[1];
	}

}

