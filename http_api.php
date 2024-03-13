<?php

require __DIR__ . '/src/WordPress/Streams/StreamPeeker.php';
require __DIR__ . '/src/WordPress/Streams/StreamPeekerContext.php';

use WordPress\Streams\StreamPeeker;
use WordPress\Streams\StreamPeekerContext;

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

function blocking_write_to_stream( $stream, $data, $timeout_microseconds = 50000) {
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

function blocking_read_headers( $stream ) {
    $headers = '';
    while ( true ) {
        $byte = blocking_read_from_stream( $stream, 1 );
        $headers .= $byte;
        if ( substr( $headers, -4 ) === "\r\n\r\n" ) {
            break;
        }
    }
    return $headers;
}

function parse_headers(string $headers) {
    $lines = explode("\r\n", $headers);
    $status = array_shift($lines);
    $status = explode(' ', $status);
    $status = [
        'protocol' => $status[0],
        'code' => $status[1],
        'message' => $status[2],
    ];
    $headers = [];
    foreach ($lines as $line) {
        if(!str_contains($line, ': ')) continue;
        $line = explode(': ', $line);
        $headers[strtolower($line[0])] = $line[1];
    }
    return [
        'status' => $status,
        'headers' => $headers,
    ];
}

function handle_response_headers(array $headers) {
    // Assume it's alright
}

function start_download( string $url, $onProgress ) {
    // Split $url into $host and $path, $path should contain the query string
    $parts = parse_url( $url );
    var_dump($parts);
    $host = $parts['host'];
    $path = $parts['path'] . (isset($parts['query']) ? '?' . $parts['query'] : '');

    $stream = open_nonblocking_socket( 'tcp://' . $host . ':443' );

    $request = <<<REQUEST
GET $path HTTP/1.1
Host: $host
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
Accept-Encoding: gzip, deflate, br
Accept-Language: en-US,en;q=0.9
Connection: close
REQUEST;
    $request = str_replace( "\n", "\r\n", $request ) . "\r\n\r\n";
    blocking_write_to_stream( $stream, $request );

    $headers_raw = blocking_read_headers($stream);
    $headers = parse_headers($headers_raw);

    // Assume the response is 200, uncompressed etc
    handle_response_headers($headers);

    $contentLength = $headers['headers']['content-length'] ?? 0;
    $handle = StreamPeeker::wrap(
        new StreamPeekerContext(
            $stream,
            function ($data) use ($onProgress, $contentLength) {
                static $streamedBytes = 0;
                $streamedBytes += strlen($data);
                $onProgress($streamedBytes, $contentLength);
			}
		)
	);

    return $handle;
}

$stream = start_download(
    // "https://downloads.wordpress.org/plugin/gutenberg.17.9.0.zip",
    "https://downloads.wordpress.org/plugin/woocommerce.8.6.1.zip",
    // "https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip",
    function ($downloaded, $total) {
        // var_dump($data);
        echo "onProgress callback ". $downloaded."/$total\n";
    }
);

$downloaded = (
    blocking_read_from_stream($stream, 1024)
    . blocking_read_from_stream($stream, 1024)
    . blocking_read_from_stream($stream, 1024)
    . blocking_read_from_stream($stream, 1024)
);
var_dump(strlen($downloaded));
file_put_contents( 'hello-dolly.zip', $downloaded );

