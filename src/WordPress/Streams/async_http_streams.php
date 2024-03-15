<?php

/**
 * @TODO Better error messages, e.g throw new AsyncResourceException($resource)
 * that would report which URL failed to download.
 */

namespace WordPress\Streams;

use Exception;
use InvalidArgumentException;

function streams_http_open_nonblocking( $urls ) {
	$streams = [];
	foreach ( $urls as $k => $url ) {
		$streams[ $k ] = stream_http_open_nonblocking( $url );
	}

	return $streams;
}

function stream_http_open_nonblocking( $url ) {
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
		$ready = @stream_select( $read, $write, $except, 0, 5000000 );
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
				$request = stream_http_prepare_request_bytes( $context['socket']['originalUrl'] );
				fwrite( $stream, $request );
				unset( $remaining_streams[ $k ] );
			}
		}
	}
}


function streams_http_response_await_bytes( $streams, $length, $timeout_microseconds = 5000000 ) {
	$read = $streams;
	if ( count( $read ) === 0 ) {
		return false;
	}
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


function parse_http_headers( string $headers ) {
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


function stream_http_prepare_request_bytes( $url ) {
	$parts = parse_url( $url );
	$host = $parts['host'];
	$path = $parts['path'] . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
	$request = <<<REQUEST
GET $path HTTP/1.1
Host: $host
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Language: en-US,en;q=0.9
Connection: keep-alive
REQUEST;

	// @TODO: Add support for Accept-Encoding: gzip

	return str_replace( "\n", "\r\n", $request ) . "\r\n\r\n";
}

function streams_http_response_await_headers( $streams ) {
	$headers = [];
	foreach ( $streams as $k => $stream ) {
		$headers[ $k ] = '';
	}
	$remaining_streams = $streams;
	while ( true ) {
		$bytes = streams_http_response_await_bytes( $remaining_streams, 1 );
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
		$headers[ $k ] = parse_http_headers( $header );
	}

	return $headers;
}

function stream_monitor_progress( $stream, $contentLength, $onProgress ) {
	return StreamPeekerWrapper::create_resource(
		new StreamPeekerData(
			$stream,
			function ( $data ) use ( $onProgress, $contentLength ) {
				static $streamedBytes = 0;
				$streamedBytes += strlen( $data );
				$onProgress( $streamedBytes, $contentLength );
			}
		)
	);
}

function streams_send_http_requests( array $requests ) {
	$urls = [];
	foreach ( $requests as $request ) {
		$urls[] = $request->url;
	}
	$redirects = $urls;
	$final_streams = [];
	$response_headers = [];
	do {
		$streams = streams_http_open_nonblocking( $redirects );
		streams_http_requests_send( $streams );

		$redirects = [];
		$headers = streams_http_response_await_headers( $streams );
		foreach ( array_keys( $headers ) as $k ) {
			if ( $headers[ $k ]['status']['code'] > 399 || $headers[ $k ]['status']['code'] < 200 ) {
				throw new Exception( "Failed to download file" );
			}
			if ( isset( $headers[ $k ]['headers']['location'] ) ) {
				$redirects[ $k ] = $headers[ $k ]['headers']['location'];
				fclose( $streams[ $k ] );
				continue;
			}

			$final_streams[ $k ] = $streams[ $k ];
			$response_headers[ $k ] = $headers[ $k ];
		}
	} while ( count( $redirects ) );

	return [ $final_streams, $response_headers ];
}
