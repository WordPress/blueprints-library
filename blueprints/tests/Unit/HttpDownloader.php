<?php

use WordPress\Blueprints\Cache\FileCache;
use WordPress\Blueprints\HttpDownloader;
use Symfony\Component\HttpClient\HttpClient;

it( 'Should download a file', function () {
	$downloader = new HttpDownloader(
		HttpClient::create(),
		$this->createMock( FileCache::class )
	);
	$stream     = $downloader->fetch( 'https://playground.wordpress.net' );
	expect( $stream )->toBeTruthy();
	$response_byte = fread( $stream, 10 );
	expect( $response_byte )->toBe( '<!DOCTYPE ' );
} );

it( 'Should download multiple files in parallel', function () {
	$downloader     = new HttpDownloader(
		HttpClient::create(),
		$this->createMock( FileCache::class )
	);
	$time_start     = microtime( true );
	$stream1        = $downloader->fetch( 'https://playground.wordpress.net' );
	$stream2        = $downloader->fetch( 'https://playground.wordpress.net' );
	$stream3        = $downloader->fetch( 'https://playground.wordpress.net' );
	$time_end       = microtime( true );
	$execution_time = ( $time_end - $time_start );
	expect( $execution_time )->toBeLessThan( 1 );
	fclose( $stream1 );
	fclose( $stream2 );
	fclose( $stream3 );
} );

it( 'Should cache the output', function () {
	$mockCache = $this->createMock( FileCache::class );
	$mockCache->expects( $this->once() )
	          ->method( 'has' )
	          ->willReturn( false );
	$mockCache->expects( $this->once() )
	          ->method( 'set' );
	$downloader = new HttpDownloader(
		HttpClient::create(),
		$mockCache
	);
	$stream     = $downloader->fetch( 'https://playground.wordpress.net' );
	stream_get_contents( $stream );
	fclose( $stream );
} );
