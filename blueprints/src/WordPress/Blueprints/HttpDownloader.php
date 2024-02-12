<?php

namespace WordPress\Blueprints;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpClient\Response\StreamWrapper;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WordPress\Streams\StreamPeeker;
use WordPress\Streams\StreamPeekerData;

class ResponseStreamPeekerData extends StreamPeekerData {

	public function __construct( public $fp, public $callback, public $on_close, protected $response ) {
	}

	public function getResponse() {
		return $this->response;
	}

}

class HttpDownloader {

	public function __construct( protected HttpClientInterface $client, protected CacheInterface $cache ) {
	}

	public function fetch( string $url ) {
		if ( $this->cache->has( $url ) ) {
			// Return a stream resource.
			// @TODO: Stream directly from the cache
			$stream = fopen( 'php://memory', 'r+' );
			fwrite( $stream, $this->cache->get( $url ) );
			rewind( $stream );

			return $stream;
		}

		$response = $this->client->request( 'GET', $url );
		$resource = StreamWrapper::createResource( $response, $this->client );
		if ( ! $resource ) {
			throw new \Exception( 'Failed to download file' );
		}

		return StreamPeeker::wrap(
			new ResponseStreamPeekerData(
				$resource,
				function ( $chunk ) use ( $url, $resource ) {
					static $bufferedChunks;
					$bufferedChunks[] = $chunk;
					if ( feof( $resource ) ) {
						// Add to the cache.
						// @TODO: don't buffer, just keep appending to the cache.
						$this->cache->set( $url, implode( '', $bufferedChunks ) );
					}
				},
				function () use ( $response ) {
					$response->cancel();
				},
				$response
			)
		);
	}

}

