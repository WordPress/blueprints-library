<?php

require __DIR__ . '/src/WordPress/Streams/StreamPeeker.php';
require __DIR__ . '/src/WordPress/Streams/StreamPeekerContext.php';
require __DIR__ . '/src/WordPress/Blueprints/Resources/ResourceMap.php';

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
	if ( $headers['status']['code'] > 399 || $headers['status']['code'] < 200 ) {
		throw new Exception( "Failed to download file" );
	}
	if ( isset( $headers['headers']['location'] ) ) {
		// @TODO: Handle redirects
		throw new Exception( "HTTP redirects are not supported yet" );
	}
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

/**
 * Groups PHP streams.
 * Whenever any stream is read, polls all the streams. Whenever other
 * streams have data before the requested stream does, it is buffered
 * for later. This means we'll read all the streams in parallel and will
 * complete the downloading faster than if we were to read them sequentially.
 */
class StreamPollingGroup {
	protected $nb_streams = 0;
	protected array $streams = [];
	protected array $buffers = [];

	public function __construct() {
	}

	public function add_stream( $stream ) {
		$key = $this->nb_streams ++;
		$this->streams[ $key ] = $stream;
		$this->buffers[ $key ] = '';

		return AsyncStreamWrapper::wrap( new AsyncStreamWrapperData( $stream, $this ) );
	}

	public function add_streams( array $streams ) {
		$wrapped = [];
		foreach ( $streams as $stream ) {
			$wrapped[] = $this->add_stream( $stream );
		}

		return $wrapped;
	}

	public function read_bytes( $stream, $length ) {
		$key = array_search( $stream, $this->streams );
		if ( false === $key ) {
			return false;
		}

		while ( true ) {
			if ( strlen( $this->buffers[ $key ] ) >= $length ) {
				$buffered = substr( $this->buffers[ $key ], 0, $length );
				$this->buffers[ $key ] = substr( $this->buffers[ $key ], $length );

				return $buffered;
			} elseif ( feof( $stream ) ) {
				$buffered = $this->buffers[ $key ];
				unset( $this->buffers[ $key ] );
				unset( $this->streams[ $key ] );

				return strlen( $buffered ) ? $buffered : false;
			}
			$remaining_length = $length - strlen( $this->buffers[ $key ] );
			$bytes = sockets_http_response_await_bytes( $this->streams, $remaining_length );
			foreach ( $bytes as $k => $chunk ) {
				$this->buffers[ $k ] .= $chunk;
			}
		}
	}
}

class VanillaStreamWrapperData {
	public $fp;

	public function __construct( $fp ) {
		$this->fp = $fp;
	}
}

class VanillaStreamWrapper {
	protected $stream;

	protected $context;

	protected $wrapper_data;

	protected static $isRegistered = false;

	const SCHEME = 'vanilla';

	static public function register() {
		if ( static::$isRegistered ) {
			return;
		}
		if ( ! stream_wrapper_register( static::SCHEME, static::class ) ) {
			throw new \Exception( 'Failed to register protocol' );
		}
		static::$isRegistered = true;
	}

	static public function unregister() {
		stream_wrapper_unregister( 'async' );
	}

	static public function wrap( VanillaStreamWrapperData $data ) {
		static::register();

		$context = stream_context_create( [
			static::SCHEME => [
				'wrapper_data' => $data,
			],
		] );

		return fopen( static::SCHEME . '://', 'r', false, $context );
	}

	public function stream_set_option( int $option, int $arg1, ?int $arg2 ): bool {
		if ( \STREAM_OPTION_BLOCKING === $option ) {
			return stream_set_blocking( $this->stream, (bool) $arg1 );
		} elseif ( \STREAM_OPTION_READ_TIMEOUT === $option ) {
			return stream_set_timeout( $this->stream, $arg1, $arg2 );
		}

		return false;
	}

	// Opens the stream
	public function stream_open( $path, $mode, $options, &$opened_path ) {
		$contextOptions = stream_context_get_options( $this->context );

		if ( ! isset( $contextOptions[ static::SCHEME ]['wrapper_data'] ) || ! is_object( $contextOptions[ static::SCHEME ]['wrapper_data'] ) ) {
			return false;
		}

		$this->wrapper_data = $contextOptions[ static::SCHEME ]['wrapper_data'];

		if ( ! $this->wrapper_data->fp ) {
			return false;
		}
		$this->stream = $this->wrapper_data->fp;

		return true;
	}

	public function stream_cast( int $cast_as ) {
		return $this->stream;
	}

	// Reads from the stream
	public function stream_read( $count ) {
		return fread( $this->stream, $count );
	}

	// Writes to the stream
	public function stream_write( $data ) {
		return fwrite( $this->stream, $data );
	}

	// Closes the stream
	public function stream_close() {
		fclose( $this->stream );
	}

	// Returns the current position of the stream
	public function stream_tell() {
		return ftell( $this->stream );
	}

	// Checks if the end of the stream has been reached
	public function stream_eof() {
		return feof( $this->stream );
	}

	// Seeks to a specific position in the stream
	public function stream_seek( $offset, $whence ) {
		return fseek( $this->stream, $offset, $whence );
	}

	// Stat information about the stream; providing dummy data
	public function stream_stat() {
		return [];
	}
}

class AsyncStreamWrapperData extends VanillaStreamWrapperData {
	public StreamPollingGroup $group;

	public function __construct( $fp, StreamPollingGroup $group ) {
		parent::__construct( $fp );
		$this->group = $group;
	}
}

class AsyncStreamWrapper extends VanillaStreamWrapper {
	const SCHEME = 'async';
	/** @var StreamPollingGroup */
	private $group;

	public function stream_open( $path, $mode, $options, &$opened_path ) {
		if ( ! parent::stream_open( $path, $mode, $options, $opened_path ) ) {
			return false;
		}

		if ( ! $this->wrapper_data->group ) {
			return false;
		}
		$this->group = $this->wrapper_data->group;

		return true;
	}

	public function stream_read( $count ) {
		return $this->group->read_bytes( $this->stream, $count );
	}

}

function start_downloads( $urls, $onProgress ) {
	$streams = streams_http_open_nonblocking( $urls );
	streams_http_requests_send( $streams );

	$stream_headers = streams_http_response_await_headers( $streams );
	foreach ( $stream_headers as $k => $headers ) {
		handle_response_headers( $headers );
	}

	return streams_monitor_progress(
		$streams,
		$stream_headers,
		$onProgress
	);
}


$onProgress = function ( $downloaded, $total ) {
	echo "Downloaded: $downloaded / $total\n";
};

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

// Download one file
file_put_contents( 'output0.zip', stream_get_contents( $streams[0] ), FILE_APPEND );

// Start more downloads
$more_streams = start_downloads( [
	"https://downloads.wordpress.org/plugin/akismet.4.1.12.zip",
	"https://downloads.wordpress.org/plugin/jetpack.10.0.zip",
	"https://downloads.wordpress.org/plugin/wordpress-seo.17.9.zip",
], $onProgress );
$more_streams = $group->add_streams( $more_streams );

// Download the rest of the files
$all_streams = array_merge( $streams, $more_streams );
foreach ( $all_streams as $k => $stream ) {
	file_put_contents( 'output' . $k . '.zip', stream_get_contents( $stream ), FILE_APPEND );
}
