<?php

require __DIR__ . '/src/WordPress/Streams/StreamPeeker.php';
require __DIR__ . '/src/WordPress/Streams/StreamPeekerContext.php';

use WordPress\Streams\StreamPeeker;
use WordPress\Streams\StreamPeekerContext;

function stream_notification_callback(
	$notification_code,
	$severity,
	$message,
	$message_code,
	$bytes_transferred,
	$bytes_max
) {
	var_dump( $notification_code );
	static $filesize = null;

	switch ( $notification_code ) {
		case STREAM_NOTIFY_RESOLVE:
		case STREAM_NOTIFY_AUTH_REQUIRED:
		case STREAM_NOTIFY_COMPLETED:
		case STREAM_NOTIFY_FAILURE:
		case STREAM_NOTIFY_AUTH_RESULT:
			/* Ignore */
			break;

		case STREAM_NOTIFY_REDIRECTED:
			echo "Being redirected to: ", $message, "\n";
			break;

		case STREAM_NOTIFY_CONNECT:
			echo "Connected...\n";
			break;

		case STREAM_NOTIFY_FILE_SIZE_IS:
			$filesize = $bytes_max;
			echo "Filesize: ", $filesize, "\n";
			break;

		case STREAM_NOTIFY_MIME_TYPE_IS:
			echo "Mime-type: ", $message, "\n";
			break;

		case STREAM_NOTIFY_PROGRESS:
			if ( $bytes_transferred > 0 ) {
				if ( ! isset( $filesize ) ) {
					printf( "\rUnknown filesize.. %2d kb done..", $bytes_transferred / 1024 );
				} else {
					$length = (int) ( ( $bytes_transferred / $filesize ) * 100 );
					printf( "\r[%-100s] %d%% (%2d/%2d kb)",
						str_repeat( "=", $length ) . ">",
						$length,
						( $bytes_transferred / 1024 ),
						$filesize / 1024 );
				}
			}
			break;
	}
}

function open_nonblocking_socket( $url ) {
	$stream = stream_socket_client(
		$url,
		$errno,
		$errstr,
		30,
		STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT
	);
	if ( $stream === false ) {
		throw new Exception( 'Unable to open stream' );
	}

	stream_socket_enable_crypto( $stream, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT );
	stream_set_blocking( $stream, 0 );

	return $stream;
}

function blocking_write_to_stream( $stream, $data, $timeout_microseconds = 50000 ) {
	$read = [];
	$write = [ $stream ];
	$except = null;
	// Silence PHP Warning: stream_select(): 960 bytes of buffered data lost during stream conversion!
	// The warning doesn't seem to be useful in this context. It is trigerred when
	// (stream->writepos - stream->readpos) > 0
	// The warning is only trigerred when we use StreamPeeker, not when dealing with
	// vanilla socket. Why?
	$ready = @stream_select( $read, $write, $except, 0, $timeout_microseconds );

	if ( $ready === false ) {
		$error = error_get_last();
		throw new Exception( "Error: " . $error['message'] );
	} elseif ( $ready > 0 ) {
		return fwrite( $stream, $data );
	} else {
		throw new Exception( "stream_select timed out" );
	}
}

function blocking_read_from_stream( $stream, $length, $timeout_microseconds = 500000 ) {
	$read = [ $stream ];
	$write = [];
	$except = null;
	$ready = @stream_select( $read, $write, $except, 0, $timeout_microseconds );

	if ( $ready === false ) {
		$error = error_get_last();
		throw new Exception( "Error: " . $error['message'] );
	} elseif ( $ready > 0 ) {
		return fread( $stream, $length );
	} else {
		throw new Exception( "stream_select timed out" );
	}
}

function poll_streams( $streams, $length, $timeout_microseconds = 500000 ) {
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

function blocking_stream_get_contents( $stream, $timeout_microseconds = 500000 ) {
	$contents = '';
	while ( true ) {
		$byte = blocking_read_from_stream( $stream, 1, $timeout_microseconds );
		$contents .= $byte;
		if ( feof( $stream ) ) {
			break;
		}
	}

	return $contents;
}

function blocking_read_headers( $stream ) {
	$headers = '';
	while ( true ) {
		$byte = blocking_read_from_stream( $stream, 1 );
		$headers .= $byte;
		if ( substr( $headers, - 4 ) === "\r\n\r\n" ) {
			break;
		}
	}

	return $headers;
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
		'status' => $status,
		'headers' => $headers,
	];
}

function handle_response_headers( array $headers ) {
	// Assume it's alright
}

function start_downloads( array $urls, $timeout_microseconds = 500000 ) {
	$streams = [];
	foreach ( $urls as $url ) {
		$streams[] = start_download( $url );
	}

	// Wait for all streams to be ready for writing
	// and then write the request bytes to each stream.
	$waiting_for_streams = [ ...$streams ];
	while ( count( $waiting_for_streams ) > 0 ) {
		$read = [];
		$write = [ ...$waiting_for_streams ];
		$except = null;
		// Silence PHP Warning: stream_select(): 960 bytes of buffered data lost during stream conversion!
		// The warning doesn't seem to be useful in this context. It is trigerred when
		// (stream->writepos - stream->readpos) > 0
		// The warning is only trigerred when we use StreamPeeker, not when dealing with
		// vanilla socket. Why?
		$ready = @stream_select( $read, $write, $except, 0, $timeout_microseconds );

		if ( $ready === false ) {
			$error = error_get_last();
			throw new Exception( "Error: " . $error['message'] );
		} elseif ( $ready > 0 ) {
			// Stop waiting for the streams that are ready for writing
			$waiting_for_streams = array_diff( $waiting_for_streams, $write );
		} else {
			throw new Exception( "stream_select timed out" );
		}

		foreach ( $write as $k => $stream ) {
			$request = prepare_request_bytes( $urls[ $k ] );
			blocking_write_to_stream( $stream, $request );
		}
	}

	return $streams;
}

function start_download( string $url ) {
	// Split $url into $host and $path, $path should contain the query string
	$parts = parse_url( $url );
	$host = $parts['host'];

	return open_nonblocking_socket( 'tcp://' . $host . ':443' );
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

function consume_headers( $stream ) {
	// Assume the response is 200, uncompressed etc
	$headers_raw = blocking_read_headers( $stream );
	$headers = parse_headers( $headers_raw );
	handle_response_headers( $headers );

	return $headers;
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

$streams = start_downloads(
	[
		"https://downloads.wordpress.org/plugin/gutenberg.17.9.0.zip",
		"https://downloads.wordpress.org/plugin/woocommerce.8.6.1.zip",
		"https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip",
	]
);

foreach ( $streams as $k => $stream ) {
	$headers = consume_headers( $stream );
	$streams[ $k ] = monitor_progress( $stream,
		$headers['headers']['content-length'],
		function ( $downloaded, $total ) {
			echo "onProgress callback " . $downloaded . "/$total\n";
		} );
	print_r( $headers );
}

while ( $results = poll_streams( $streams, 1024 ) ) {
	foreach ( $results as $k => $chunk ) {
		file_put_contents( 'output' . $k . '.zip', $chunk, FILE_APPEND );
	}
}
