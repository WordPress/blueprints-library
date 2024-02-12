<?php

require 'vendor/autoload.php';

use WordPress\Blueprints\Resources\PHPStreamResource;
use Symfony\Component\HttpClient\HttpClient;
use WordPress\Blueprints\Cache\FileCache;
use WordPress\Blueprints\HttpDownloader;
use WordPress\Blueprints\ProgressEvent;
use WordPress\DataSource\HttpSource;

$client     = HttpClient::create();
$downloader = new HttpDownloader( $client, new FileCache() );

$downloader->events->addListener(
	ProgressEvent::class,
	function ( ProgressEvent $event ) {
		echo $event->url . ' ' . $event->downloadedBytes . '/' . $event->totalBytes . PHP_EOL;
	}
);

$urls = [
	'https://downloads.wordpress.org/plugin/woocommerce.zip',
	'https://downloads.wordpress.org/plugin/gutenberg.zip',
	'https://downloads.wordpress.org/plugin/akismet.zip',
];

$fps = [];
foreach ( $urls as $url ) {
	$fps[] = ( new HttpSource( $client, new FileCache(), $url ) )->stream();
}

$woo_resource = new PHPStreamResource( $fps[0] );
$woo_resource->saveTo( 'woocommerce.zip' );

$gut_resource = new PHPStreamResource( $fps[1] );
$gut_resource->saveTo( 'gutenberg.zip' );

//fread( $fps[0], 1024 );
//fread( $fps[1], 1024 );
//fread( $fps[2], 1024 );
//
//stream_get_contents( $fps[2] );
//stream_get_contents( $fps[0] );
//stream_get_contents( $fps[1] );

// stream_select doesn't work with the Symfony HTTP stream
//$w = null;
//$e = null;
//while ( true ) {
//	$streamnb = stream_select( $fps, $w, $e, null );
//	if ( $streamnb === false ) {
//		break;
//	}
//	die( "A" );
//	fread( $fps[ $streamnb ], 1024 );
//	if ( feof( $fps[ $streamnb ] ) ) {
//		fclose( $fps[ $streamnb ] );
//		unset( $fps[ $streamnb ] );
//	}
//}
