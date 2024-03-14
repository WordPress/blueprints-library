<?php

require __DIR__ . '/src/WordPress/Streams/StreamPeeker.php';
require __DIR__ . '/src/WordPress/Streams/StreamPeekerContext.php';

use WordPress\Streams\StreamPeeker;
use WordPress\Streams\StreamPeekerContext;

function streams_http_open_nonblocking( $urls ) {
	$streams = [];
	foreach ( $urls as $url ) {
		$streams[] = open_http_stream( $url );
	}

	return $streams;
}

function open_http_stream( $url ) {
	$parts = parse_url( $url );
	$scheme = $parts['scheme'];
	if ( ! in_array( $scheme, [ 'http', 'https' ] ) ) {
		throw new InvalidArgumentException( 'Invalid scheme – only http:// and https:// URLs are supported' );
	}

	$port = $parts['port'] ?? ( $scheme === 'https' ? 443 : 80 );
	$host = $parts['host'];

	// Create stream context
	$context = stream_context_create(
		[
			'socket' => [
				'isSsl'       => $scheme === 'https',
				'originalUrl' => $url,
				'socketUrl'   => 'tcp://' . $host . ':' . $port,
			],
		]
	);
	var_dump( 'tcp://' . $host . ':' . $port );
	$stream = stream_socket_client(
		'tcp://' . $host . ':' . $port,
		$errno,
		$errstr,
		30,
		STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT,
		$context
	);
	if ( $stream === false ) {
		throw new Exception( 'Unable to open stream' );
	}

	stream_set_blocking( $stream, 0 );

	return $stream;
}

function streams_http_requests_send( $streams ) {
	$read = $except = null;
	$remaining_streams = $streams;
	while ( count( $remaining_streams ) ) {
		$write = $remaining_streams;
		$ready = @stream_select( $read, $write, $except, 0, 500000 );
		if ( $ready === false ) {
			$error = error_get_last();
			throw new Exception( "Error: " . $error['message'] );
		} elseif ( $ready <= 0 ) {
			throw new Exception( "stream_select timed out" );
		}

		foreach ( $write as $k => $stream ) {
			$enabled_crypto = stream_socket_enable_crypto( $stream, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT );
			if ( false === $enabled_crypto ) {
				throw new Exception( "Failed to enable crypto" );
			} elseif ( 0 === $enabled_crypto ) {
				// Wait for the handshake to complete
			} else {
				// SSL handshake complete, send the request headers
				$context = stream_context_get_options( $stream );
				$request = prepare_request_bytes( $context['socket']['originalUrl'] );
				fwrite( $stream, $request );
				unset( $remaining_streams[ $k ] );
			}
		}
	}
}


function sockets_http_response_await_bytes( $streams, $length, $timeout_microseconds = 500000 ) {
	$active_streams = array_filter( $streams, function ( $stream ) {
		return ! feof( $stream );
	} );
	if ( empty( $active_streams ) ) {
		return false;
	}

	$read = $active_streams;
	$write = [];
	$except = null;
	$ready = @stream_select( $read, $write, $except, 0, $timeout_microseconds );

	if ( $ready === false ) {
		$error = error_get_last();
		throw new Exception( "Error: " . $error['message'] );
	} elseif ( $ready <= 0 ) {
		throw new Exception( "stream_select timed out" );
	}

	$chunks = [];
	foreach ( $read as $k => $stream ) {
		$chunks[ $k ] = fread( $stream, $length );
	}

	return $chunks;
}


function parse_headers( string $headers ) {
	$lines = explode( "\r\n", $headers );
	$status = array_shift( $lines );
	$status = explode( ' ', $status );
	$status = [
		'protocol' => $status[0],
		'code'     => $status[1],
		'message'  => $status[2],
	];
	$headers = [];
	foreach ( $lines as $line ) {
		if ( ! str_contains( $line, ': ' ) ) {
			continue;
		}
		$line = explode( ': ', $line );
		$headers[ strtolower( $line[0] ) ] = $line[1];
	}

	return [
		'status'  => $status,
		'headers' => $headers,
	];
}

function handle_response_headers( array $headers ) {
	// Assume it's alright
}

function prepare_request_bytes( $url ) {
	$parts = parse_url( $url );
	$host = $parts['host'];
	$path = $parts['path'] . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
	$request = <<<REQUEST
GET $path HTTP/1.1
Host: $host
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9
Connection: keep-alive
REQUEST;

	return str_replace( "\n", "\r\n", $request ) . "\r\n\r\n";
}

function streams_http_response_await_headers( $streams ) {
	$headers = [];
	foreach ( $streams as $k => $stream ) {
		$headers[ $k ] = '';
	}
	$remaining_streams = $streams;
	while ( true ) {
		$bytes = sockets_http_response_await_bytes( $remaining_streams, 1 );
		if ( false === $bytes ) {
			break;
		}
		foreach ( $bytes as $k => $byte ) {
			$headers[ $k ] .= $byte;
			if ( str_ends_with( $headers[ $k ], "\r\n\r\n" ) ) {
				unset( $remaining_streams[ $k ] );
			}
		}
	}

	foreach ( $headers as $k => $header ) {
		$headers[ $k ] = parse_headers( $header );
		handle_response_headers( $headers[ $k ] );
	}

	return $headers;
}

function streams_monitor_progress( $streams, $headers, $onProgress ) {
	$monitored = [];
	foreach ( $streams as $k => $stream ) {
		$monitored[ $k ] = monitor_progress(
			$stream,
			$headers[ $k ]['headers']['content-length'],
			function ( $downloaded, $total ) use ( $onProgress ) {
				$onProgress( $downloaded, $total );
			}
		);
	}

	return $monitored;
}

function monitor_progress( $stream, $contentLength, $onProgress ) {
	return StreamPeeker::wrap(
		new StreamPeekerContext(
			$stream,
			function ( $data ) use ( $onProgress, $contentLength ) {
				static $streamedBytes = 0;
				$streamedBytes += strlen( $data );
				$onProgress( $streamedBytes, $contentLength );
			}
		)
	);
}

$streams = streams_http_open_nonblocking( [
	"https://downloads.wordpress.org/plugin/gutenberg.17.9.0.zip",
	"https://downloads.wordpress.org/plugin/woocommerce.8.6.1.zip",
	"https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip",
] );
streams_http_requests_send( $streams );

$headers = streams_http_response_await_headers( $streams );
$streams = streams_monitor_progress(
	$streams,
	$headers,
	function ( $downloaded, $total ) {
		echo "onProgress callback " . $downloaded . "/$total\n";
	}
);
print_r( $headers );

while ( $results = sockets_http_response_await_bytes( $streams, 1024 ) ) {
	foreach ( $results as $k => $chunk ) {
		file_put_contents( 'output' . $k . '.zip', $chunk, FILE_APPEND );
	}
}
