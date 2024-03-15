<?php

namespace WordPress\DataSource;

use Psr\SimpleCache\CacheInterface;
use WordPress\Streams\AsyncHttpClient;
use WordPress\Streams\Request;
use WordPress\Streams\StreamPeekerWrapper;
use WordPress\Streams\StreamPeekerData;

class UrlSource extends BaseDataSource {

	public function __construct(
		protected AsyncHttpClient $client,
		protected CacheInterface $cache
	) {
		parent::__construct();
		$client->set_progress_callback( function ( Request $request, $downloaded, $total ) {
			$this->events->dispatch( new DataSourceProgressEvent(
				$request->url,
				$downloaded,
				$total
			) );
		} );
	}

	public function stream( $resourceIdentifier ) {
		$url = $resourceIdentifier;
		// @TODO: Enable cache
		if ( false && $this->cache->has( $url ) ) {
			// Return a stream resource.
			// @TODO: Stream directly from the cache
			$cached = $this->cache->get( $url );
			$data_size = strlen( $cached );
			$this->events->dispatch( new DataSourceProgressEvent(
				$url,
				$data_size,
				$data_size
			) );
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
			static $bufferedChunks = [];
			$bufferedChunks[] = $chunk;
			if ( feof( $stream ) ) {
				$this->cache->set( $url, implode( '', $bufferedChunks ) );
				$bufferedChunks = [];
			}
		};

		return StreamPeekerWrapper::create_resource(
			new StreamPeekerData(
				$stream,
				$onChunk,
			)
		);
	}

}
