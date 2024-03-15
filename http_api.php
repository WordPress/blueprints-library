<?php

use WordPress\Streams\StreamPollingGroup;
use function WordPress\Streams\start_downloads;

require __DIR__ . '/vendor/autoload.php';

$onProgress = function ( $downloaded, $total ) {
	echo "Downloaded: $downloaded / $total\n";
};

// Initiate a bunch of HTTPS requests
$streams = start_downloads( [
	"https://downloads.wordpress.org/plugin/gutenberg.17.9.0.zip",
	"https://downloads.wordpress.org/plugin/woocommerce.8.6.1.zip",
	"https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip",
], $onProgress );

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

// Non-blocking parallelized sequential processing – the second fastest method.
// Polls all the streams when any stream is read.
$group = new StreamPollingGroup();
$streams = $group->add_streams( $streams );

// Stream a single file, while streaming all the files
file_put_contents( 'output0.zip', stream_get_contents( $streams[0] ) );

// Initiate more HTTPS requests
$more_streams = $group->add_streams(
	start_downloads( [
		"https://downloads.wordpress.org/plugin/akismet.4.1.12.zip",
		"https://downloads.wordpress.org/plugin/jetpack.10.0.zip",
		"https://downloads.wordpress.org/plugin/wordpress-seo.17.9.zip",
	], $onProgress )
);

// Download the rest of the files. Foreach() seems like downloading things
// sequentially, but we're actually streaming all the files in parallel.
foreach ( array_merge( $streams, $more_streams ) as $k => $stream ) {
	file_put_contents( 'output' . $k . '.zip', stream_get_contents( $stream ) );
}
