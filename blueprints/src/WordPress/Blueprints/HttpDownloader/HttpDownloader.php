<?php

namespace WordPress\Blueprints\HttpDownloader;

use blueprints\src\WordPress\Blueprints\Resources\ResponseStreamResource;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


class HttpDownloader {

	/** @var ResponseInterface[] */
	protected $requests = [];
	protected $requestId = 0;

	public function __construct( protected HttpClientInterface $client, protected CacheInterface $cache ) {
	}

	public function add( string $url ) {
		if ( $this->cache->has( $url ) ) {
			$contents = $this->cache->get( $url );

			return $contents;
		}

		$response                     = $this->client->request( 'GET', $url );
		$requestId                    = ++ $this->requestId;
		$this->requests[ $requestId ] = $response;

		return new ResponseStreamResource( $this->client->stream( $response ) );
	}

//	public function stream( $requestId ) {
//		$response = $this->requests[ $requestId ];
//
//		$responseChunks = [];
//		foreach ( $this->client->stream( $response ) as $chunk ) {
//			yield $chunk;
//			$responseChunks[] = $chunk->getContent();
//		}
//
//		$this->cache->set(
//			$response->getInfo( 'url' ),
//			implode( '', $responseChunks[ $requestId ] )
//		);
//	}

}

