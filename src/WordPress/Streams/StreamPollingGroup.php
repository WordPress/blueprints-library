<?php

namespace WordPress\Streams;

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

	public function add_streams( array $streams ) {
		$wrapped = [];
		foreach ( $streams as $stream ) {
			$wrapped[] = $this->add_stream( $stream );
		}

		return $wrapped;
	}

	public function add_stream( $stream ) {
		$key = $this->nb_streams ++;
		$this->streams[ $key ] = $stream;
		$this->buffers[ $key ] = '';

		return AsyncStreamWrapper::wrap( new AsyncStreamWrapperData( $stream, $this ) );
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
			$bytes = streams_http_response_await_bytes( $this->streams, $remaining_length );
			foreach ( $bytes as $k => $chunk ) {
				$this->buffers[ $k ] .= $chunk;
			}
		}
	}
}
