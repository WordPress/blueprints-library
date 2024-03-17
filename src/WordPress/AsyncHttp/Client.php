<?php

namespace WordPress\AsyncHttp;

use WordPress\Util\Map;
use function WordPress\Streams\stream_monitor_progress;
use function WordPress\Streams\streams_http_response_await_bytes;
use function WordPress\Streams\streams_send_http_requests;

/**
 * Groups PHP streams.
 * Whenever any stream is read, polls all the streams. Whenever other
 * streams have data before the requested stream does, it is buffered
 * for later. This means we'll read all the streams in parallel and will
 * complete the downloading faster than if we were to read them sequentially.
 */
class Client {
	protected $concurrency = 10;
	protected Map $requests;
	protected $onProgress;
	protected $queue_needs_processing = false;

	public function __construct() {
		$this->requests = new Map();
		$this->onProgress = function () {
		};
	}

	public function set_progress_callback( $onProgress ) {
		$this->onProgress = $onProgress;
	}

	/**
	 * Enqueues one or multiple HTTP requests for asynchronous processing.
	 *
	 * @param mixed $requests The HTTP request(s) to enqueue. Can be a single request or an array of requests.
	 *
	 * @return array The enqueued streams.
	 */
	public function enqueue( $requests ) {
		if ( ! is_array( $requests ) ) {
			return $this->enqueue_request( $requests );
		}

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
		$stream = StreamWrapper::create_resource(
			new StreamData( $request, $this )
		);
		$this->requests[ $request ] = new RequestInfo( $stream );
		$this->queue_needs_processing = true;

		return $stream;
	}

	/**
	 * Starts a next batch of enqueued requests up to the concurrency limit.
	 *
	 * @return void
	 */
	public function process_queue() {
		$this->queue_needs_processing = false;

		$active_requests = count( $this->get_streamed_requests() );
		$backfill = $this->concurrency - $active_requests;
		if ( $backfill <= 0 ) {
			return;
		}

		$enqueued = array_slice( $this->get_enqueued_request(), 0, $backfill );
		list( $streams, $response_headers ) = streams_send_http_requests( $enqueued );

		foreach ( $streams as $k => $stream ) {
			$request = $enqueued[ $k ];
			$total = $response_headers[ $k ]['headers']['content-length'];
			$this->requests[ $request ]->state = RequestInfo::STATE_STREAMING;
			$this->requests[ $request ]->stream = stream_monitor_progress(
				$stream,
				function ( $downloaded ) use ( $request, $total ) {
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

	protected function get_streamed_requests() {
		$active_requests = [];
		foreach ( $this->requests as $request => $info ) {
			if ( $info->state !== RequestInfo::STATE_ENQUEUED ) {
				$active_requests[] = $request;
			}
		}

		return $active_requests;
	}

	/**
	 * Reads up to $length bytes from the stream while polling all the active streams.
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

		$active_requests = $this->get_streamed_requests();
		$active_streams = array_map( function ( $request ) {
			return $this->requests[ $request ]->stream;
		}, $active_requests );

		if ( ! count( $active_streams ) ) {
			return false;
		}

		while ( true ) {
			if ( ! $request_info->isFinished() && feof( $stream ) ) {
				$request_info->state = RequestInfo::STATE_FINISHED;
				fclose( $stream );
				$this->queue_needs_processing = true;
			}

			if ( strlen( $request_info->buffer ) >= $length ) {
				$buffered = substr( $request_info->buffer, 0, $length );
				$request_info->buffer = substr( $request_info->buffer, $length );

				return $buffered;
			} elseif ( $request_info->isFinished() ) {
				unset( $this->requests[ $request ] );

				return $request_info->buffer;
			}

			$active_streams = array_filter( $active_streams, function ( $stream ) {
				return ! feof( $stream );
			} );
			if ( ! count( $active_streams ) ) {
				continue;
			}
			$bytes = streams_http_response_await_bytes(
				$active_streams,
				$length - strlen( $request_info->buffer )
			);
			foreach ( $bytes as $k => $chunk ) {
				$this->requests[ $active_requests[ $k ] ]->buffer .= $chunk;
			}
		}
	}
}
