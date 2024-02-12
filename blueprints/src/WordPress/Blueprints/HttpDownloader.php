<?php

namespace WordPress\Blueprints;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpClient\Response\StreamWrapper;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WordPress\Streams\StreamPeeker;
use WordPress\Streams\StreamPeekerData;

class ResponseStreamPeekerData extends StreamPeekerData {

	public function __construct( public $fp, public $onChunk, public $onClose, protected $response ) {
	}

	public function getResponse() {
		return $this->response;
	}

}

class ProgressEvent extends Event {
	public function __construct(
		public string $url,
		public int $downloadedBytes,
		public int|null $totalBytes
	) {
	}
}

class HttpDownloader {

	public EventDispatcher $events;

	public function __construct( protected HttpClientInterface $client, protected CacheInterface $cache ) {
		$this->events = new EventDispatcher();
	}

	public function fetch( string $url ) {
		if ( $this->cache->has( $url ) ) {
			// Return a stream resource.
			// @TODO: Stream directly from the cache
			$cached    = $this->cache->get( $url );
			$data_size = strlen( $cached );
			$this->events->dispatch( new ProgressEvent(
				$url,
				$data_size,
				$data_size
			) );
			$stream = fopen( 'php://memory', 'r+' );
			fwrite( $stream, $cached );
			rewind( $stream );

			return $stream;
		}

		$response = $this->client->request( 'GET', $url, [
			'on_progress' => function ( int $dlNow, int $dlSize, array $info ) use ( $url ): void {
				$this->events->dispatch( new ProgressEvent(
					$url,
					$dlNow,
					$dlSize
				) );
			},
		] );
		$stream   = StreamWrapper::createResource( $response, $this->client );
		if ( ! $stream ) {
			throw new \Exception( 'Failed to download file' );
		}
		$onChunk = function ( $chunk ) use ( $url, $response, $stream ) {
			// Handle response caching
			// @TODO: don't buffer, just keep appending to the cache.
			static $bufferedChunks = [];
			$bufferedChunks[] = $chunk;
			if ( feof( $stream ) ) {
				$this->cache->set( $url, implode( '', $bufferedChunks ) );
			}
		};
		$onClose = function () use ( $response ) {
			$response->cancel();
		};

		return StreamPeeker::wrap(
			new ResponseStreamPeekerData(
				$stream,
				$onChunk,
				$onClose,
				$response
			)
		);
	}


	/**
	 * This much simpler version throws a "Idle timeout reached" exception
	 * when reading from the stream :(
	 *
	 * @param string $url
	 *
	 * @return mixed
	 */
	public function fetchBugWithIdleTimeout( string $url ) {
		$passthru = function ( ChunkInterface $chunk, AsyncContext $context ) use ( $url ): \Generator {
			static $bufferedChunks = [];
			$bufferedChunks[] = $chunk->getContent();
			if ( $chunk->isLast() ) {
				$this->cache->set( $url, implode( '', $bufferedChunks ) );
			}
			// do what you want with chunks, e.g. split them
			// in smaller chunks, group them, skip some, etc.
			yield $chunk;
		};

		$response = new AsyncResponse( $this->client, 'GET', $url, [
			'timeout'     => 60000,
			'on_progress' => function ( int $dlNow, int $dlSize, array $info ) use ( $url ): void {
				$this->events->dispatch( new ProgressEvent(
					$url,
					$dlNow,
					$dlSize
				) );
			},
		], $passthru );

		return $response->toStream();
	}
}

function get_header( array $headers, string $header ) {
	foreach ( $headers as $line ) {
		$parts = explode( ': ', $line );
		if ( count( $parts ) === 2 && strtolower( $parts[0] ) === strtolower( $header ) ) {
			return $parts[1];
		}
	}
}
/*
class HttpDownloader {

	public EventDispatcher $events;

	public function __construct( protected HttpClientInterface $client ) {
		$this->events = new EventDispatcher();
	}

	public function fetch( string $url ) {
		$response = $this->client->request( 'GET', $url, [
			'on_progress' => function ( int $dlNow, int $dlSize, array $info ) use ( $url ): void {
				$this->events->dispatch( new ProgressEvent(
					$url,
					$dlNow,
					$dlSize
				) );
			},
		] );
		$stream   = StreamWrapper::createResource( $response, $this->client );
		if ( ! $stream ) {
			throw new \Exception( 'Failed to download file' );
		}

		return $stream;
	}

}
 */
