<?php

namespace WordPress\Streams;

use WordPress\Util\Map;

class RequestInfo {
	const STATE_ENQUEUED = 'STATE_ENQUEUED';
	const STATE_STREAMING = 'STATE_STREAMING';
	public $state = self::STATE_ENQUEUED;
	public $stream;
	public $buffer = '';

	/**
	 * @param $stream
	 */
	public function __construct( $stream ) {
		$this->stream = $stream;
	}
	
}

/**
 * Groups PHP streams.
 * Whenever any stream is read, polls all the streams. Whenever other
 * streams have data before the requested stream does, it is buffered
 * for later. This means we'll read all the streams in parallel and will
 * complete the downloading faster than if we were to read them sequentially.
 */
class AsyncHttpClient {
	protected $concurrency = 10;
	protected Map $requests;
	protected $onProgress;
	protected $queue_needs_processing = false;

	public function __construct( $onProgress ) {
		$this->requests = new Map();
		$this->onProgress = $onProgress;
	}

	public function enqueue( array $requests ) {
		$enqueued_streams = array();
		foreach ( $requests as $request ) {
			$enqueued_streams[] = $this->enqueue_request( $request );
		}

		return $enqueued_streams;
	}

	/**
	 * Calling this function for a bunch of streams that are enqueued but not active
	 * yet may lead to exceeding the concurrency limit if those streams are then
	 * immediately read from.
	 *
	 * In practice, it should be fine. The Blueprint executor will consume
	 * the streams in the same order as they were enqueued.
	 *
	 * @param $request
	 */
	public function get_stream( Request $request ) {
		if ( ! isset( $this->requests[ $request ] ) ) {
			$this->enqueue_request( $request );
		}

		if ( $this->queue_needs_processing ) {
			$this->process_queue();
		}

		return $this->requests[ $request ]->stream;
	}

	protected function enqueue_request( Request $request ) {
		$stream = AsyncHttpStreamWrapper::create_resource(
			new AsyncHttpStreamData( $request, $this )
		);
		$this->requests[ $request ] = new RequestInfo( $stream );
		$this->queue_needs_processing = true;

		return $stream;
	}

	public function process_queue() {
		$this->queue_needs_processing = false;

		$active_requests = count( $this->get_active_requests() );
		$backfill = $this->concurrency - $active_requests;
		if ( $backfill <= 0 ) {
			return;
		}

		$enqueued = array_slice( $this->get_enqueued_request(), 0, $backfill );
		list( $streams, $response_headers ) = streams_send_http_requests( $enqueued );

		foreach ( $streams as $k => $stream ) {
			$request = $enqueued[ $k ];
			$this->requests[ $request ]->state = RequestInfo::STATE_STREAMING;
			$this->requests[ $request ]->stream = stream_monitor_progress(
				$stream,
				$response_headers[ $k ]['headers']['content-length'],
				function ( $downloaded, $total ) use ( $request ) {
					$onProgress = $this->onProgress;
					$onProgress( $request, $downloaded, $total );
				}
			);
		}
	}

	protected function get_enqueued_request() {
		$enqueued_requests = [];
		foreach ( $this->requests as $request => $info ) {
			if ( $info->state === RequestInfo::STATE_ENQUEUED ) {
				$enqueued_requests[] = $request;
			}
		}

		return $enqueued_requests;
	}

	protected function get_active_requests() {
		$active_requests = [];
		foreach ( $this->requests as $request => $info ) {
			if ( $info->state === RequestInfo::STATE_STREAMING ) {
				$active_requests[] = $request;
			}
		}

		return $active_requests;
	}

	/**
	 * Reads $length bytes from the stream while polling all the active streams.
	 *
	 * @param $stream
	 * @param $length
	 *
	 * @return false|mixed|string
	 * @throws \Exception
	 */
	public function read_bytes( $request, $length ) {
		if ( ! isset( $this->requests[ $request ] ) ) {
			return false;
		}

		if ( $this->queue_needs_processing ) {
			$this->process_queue();
		}

		$request_info = $this->requests[ $request ];
		$stream = $request_info->stream;
		while ( true ) {
			if ( strlen( $request_info->buffer ) >= $length ) {
				$buffered = substr( $request_info->buffer, 0, $length );
				$request_info->buffer = substr( $request_info->buffer, $length );

				return $buffered;
			} elseif ( feof( $stream ) ) {
				$buffered = $request_info->buffer;
				unset( $this->requests[ $request ] );
				fclose( $stream );

				$this->queue_needs_processing = true;

				return strlen( $buffered ) ? $buffered : false;
			}
			$remaining_length = $length - strlen( $request_info->buffer );

			$active_requests = $this->get_active_requests();
			$active_streams = array_map( function ( $request ) {
				return $this->requests[ $request ]->stream;
			}, $active_requests );

			$bytes = streams_http_response_await_bytes( $active_streams, $remaining_length );
			foreach ( $bytes as $k => $chunk ) {
				$this->requests[ $active_requests[ $k ] ]->buffer .= $chunk;
			}
		}
	}
}
