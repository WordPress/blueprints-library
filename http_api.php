<?php

use WordPress\Streams\AsyncHttpStreamData;
use WordPress\Streams\AsyncHttpStreamWrapper;
use WordPress\Streams\AsyncHttpClient;
use WordPress\Streams\Request;
use function WordPress\Streams\start_downloads;
use function WordPress\Streams\stream_http_open_nonblocking;

require __DIR__ . '/vendor/autoload.php';

$client = new AsyncHttpClient( function ( Request $request, $downloaded, $total ) {
//	echo "$request->url – Downloaded: $downloaded / $total\n";
} );
echo 'Before enqueueing 1' . PHP_EOL;
$streams = $client->enqueue( [
	new Request( "https://downloads.wordpress.org/plugin/akismet.4.1.12.zip" ),
	new Request( "https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip" ),
] );
// Enqueuing another request here is instant and won't start the download yet.
//echo 'Before enqueueing 2' . PHP_EOL;
//$streams2 = $client->enqueue( [
//	new Request( "https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip" ),
//] );
//$streams = array_merge( $streams1, $streams2 );

// Stream a single file, while streaming all the files
file_put_contents( 'output0.zip', stream_get_contents( $streams[0] ) );
die();
// Initiate more HTTPS requests
echo 'Before enqueueing 3' . PHP_EOL;
$streams3 = $client->enqueue( [
	new Request( "https://downloads.wordpress.org/plugin/akismet.4.1.12.zip" ),
	new Request( "https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip" ),
	new Request( "https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip" ),
//	new Request( "https://downloads.wordpress.org/plugin/akismet.4.1.12.zip" ),
//	new Request( "https://downloads.wordpress.org/plugin/jetpack.10.0.zip" ),
//	new Request( "https://downloads.wordpress.org/plugin/wordpress-seo.17.9.zip" ),
] );
$streams = array_slice( array_merge( $streams, $streams3 ), 1 );
echo 'After enqueueing 3' . PHP_EOL;

// Download the rest of the files. Foreach() seems like downloading things
// sequentially, but we're actually streaming all the files in parallel.
foreach ( $streams as $k => $stream ) {
	file_put_contents( 'output' . $k . '.zip', stream_get_contents( $stream ) );
}

echo "Done! :)";

// ----------------------------
//
// Previous explorations:

// Non-blocking parallel processing – the fastest method.
//while ( $results = sockets_http_response_await_bytes( $streams, 8096 ) ) {
//	foreach ( $results as $k => $chunk ) {
//		file_put_contents( 'output' . $k . '.zip', $chunk, FILE_APPEND );
//	}
//}

// Blocking sequential processing – the slowest method.
//foreach ( $streams as $k => $stream ) {
//	stream_set_blocking( $stream, 1 );
//	file_put_contents( 'output' . $k . '.zip', stream_get_contents( $stream ) );
//}
