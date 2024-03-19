<?php

namespace WordPress\DataSource;

use Psr\SimpleCache\CacheInterface;
use WordPress\AsyncHttp\Client;
use WordPress\AsyncHttp\Request;
use WordPress\Streams\StreamPeekerData;
use WordPress\Streams\StreamPeekerWrapper;

class UrlSource extends BaseDataSource {

	protected $client;
	protected $cache;

	public function __construct(
		Client $client,
		CacheInterface $cache
	) {
		$this->client = $client;
		$this->cache  = $cache;

		parent::__construct();
		$client->set_progress_callback(
			function ( Request $request, $downloaded, $total ) {
				$this->events->dispatch(
					DataSourceProgressEvent::class,
					new DataSourceProgressEvent(
						$request->url,
						$downloaded,
						$total
					)
				);
			}
		);
	}

	public function stream( $resourceIdentifier ) {
		$url = $resourceIdentifier;
		// @TODO: Enable cache
		if ( false && $this->cache->has( $url ) ) {
			// Return a stream resource.
			// @TODO: Stream directly from the cache
			$cached    = $this->cache->get( $url );
			$data_size = strlen( $cached );
			$this->events->dispatch(
				DataSourceProgressEven::class,
				new DataSourceProgressEvent(
					$url,
					$data_size,
					$data_size
				)
			);
			$stream = fopen( 'php://memory', 'r+' );
			fwrite( $stream, $cached );
			rewind( $stream );

			return $stream;
		}

		$stream = $this->client->enqueue( new Request( $url ) );

		return $stream;

		// Cache
		$onChunk = function ( $chunk ) use ( $url, $stream ) {
			// Handle response caching
			static $bufferedChunks = array();
			$bufferedChunks[]      = $chunk;
			if ( feof( $stream ) ) {
				$this->cache->set( $url, implode( '', $bufferedChunks ) );
				$bufferedChunks = array();
			}
		};

		return StreamPeekerWrapper::create_resource(
			new StreamPeekerData(
				$stream,
				$onChunk
			)
		);
	}
}
