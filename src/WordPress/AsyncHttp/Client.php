<?php

namespace WordPress\AsyncHttp;

use Exception;
use WordPress\Util\Map;
use function WordPress\Streams\stream_monitor_progress;
use function WordPress\Streams\streams_http_response_await_bytes;
use function WordPress\Streams\streams_send_http_requests;

/**
 * An asynchronous HTTP client library designed for WordPress. Main features:
 *
 * **Streaming support**
 * Enqueuing a request returns a PHP resource that can be read by PHP functions like `fopen()`
 * and `stream_get_contents()`
 *
 * ```php
 * $client = new AsyncHttpClient();
 * $fp = $client->enqueue(
 *      new Request( "https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip" ),
 * );
 * // Read some data
 * $first_4_kilobytes = fread($fp, 4096);
 * // We've only waited for the first four kilobytes. The download
 * // is still in progress at this point, and yet we're free to do
 * // other work.
 * ```
 *
 * **Delayed execution and concurrent downloads**
 * The actual socket are not open until the first time the stream is read from:
 *
 * ```php
 * $client = new AsyncHttpClient();
 * // Enqueuing the requests does not start the data transmission yet.
 * $batch = $client->enqueue( [
 *     new Request( "https://downloads.wordpress.org/plugin/gutenberg.17.7.0.zip" ),
 *     new Request( "https://downloads.wordpress.org/theme/pendant.zip" ),
 * ] );
 * // Even though stream_get_contents() will return just the response body for
 * // one request, it also opens the network sockets and starts streaming
 * // both enqueued requests. The response data for $batch[1] is buffered.
 * $gutenberg_zip = stream_get_contents( $batch[0] )
 *
 * // At least a chunk of the pendant.zip have already been downloaded, let's
 * // wait for the rest of the data:
 * $pendant_zip = stream_get_contents( $batch[1] )
 * ```
 *
 * **Concurrency limits**
 * The `AsyncHttpClient` will only keep up to `$concurrency` connections open. When one of the
 * requests finishes, it will automatically start the next one.
 *
 * For example:
 * ```php
 * $client = new AsyncHttpClient();
 * // Process at most 10 concurrent request at a time.
 * $client->set_concurrency_limit( 10 );
 * ```
 *
 * **Progress monitoring**
 * A developer-provided callback (`AsyncHttpClient->set_progress_callback()`) receives progress
 * information about every HTTP request.
 *
 * ```php
 * $client = new AsyncHttpClient();
 * $client->set_progress_callback( function ( Request $request, $downloaded, $total ) {
 *      // $total is computed based on the Content-Length response header and
 *      // null if it's missing.
 *      echo "$request->url – Downloaded: $downloaded / $total\n";
 * } );
 * ```
 *
 * **HTTPS support**
 * TLS connections work out of the box.
 *
 * **Non-blocking sockets**
 * The act of opening each socket connection is non-blocking and happens nearly
 * instantly. The streams themselves are also set to non-blocking mode via `stream_set_blocking($fp, 0);`
 *
 * **Asynchronous downloads**
 * Start downloading now, do other work in your code, only block once you need the data.
 *
 * **PHP 7.0 support and no dependencies**
 * `AsyncHttpClient` works on any WordPress installation with vanilla PHP only.
 * It does not require any PHP extensions, CURL, or any external PHP libraries.
 *
 * **Supports custom request headers and body**
 */
class Client {
	protected $concurrency = 10;
	protected $requests;
	protected $onProgress;
	protected $queue_needs_processing = false;

	public function __construct() {
		$this->requests   = new Map();
		$this->onProgress = function () {
		};
	}

	/**
	 * Sets the limit of concurrent connections this client will open.
	 *
	 * @param int $concurrency
	 */
	public function set_concurrency_limit( $concurrency ) {
		$this->concurrency = $concurrency;
	}

	/**
	 * Sets the callback called when response bytes are received on any of the enqueued
	 * requests.
	 *
	 * @param callable $onProgress A function of three arguments:
	 *                             Request $request, int $downloaded, int $total.
	 */
	public function set_progress_callback( $onProgress ) {
		$this->onProgress = $onProgress;
	}

	/**
	 * Enqueues one or multiple HTTP requests for asynchronous processing.
	 * It does not open the network sockets, only adds the Request objects to
	 * an internal queue. Network transmission is delayed until one of the returned
	 * streams is read from.
	 *
	 * @param Request|Request[] $requests The HTTP request(s) to enqueue. Can be a single request or an array of requests.
	 *
	 * @return resource|array The enqueued streams.
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
	 * Returns the response stream associated with the given Request object.
	 * Enqueues the Request if it hasn't been enqueued yet.
	 *
	 * @param Request $request
	 *
	 * @return resource
	 */
	public function get_stream( $request ) {
		if ( ! isset( $this->requests[ $request ] ) ) {
			$this->enqueue_request( $request );
		}

		if ( $this->queue_needs_processing ) {
			$this->process_queue();
		}

		return $this->requests[ $request ]->stream;
	}

	/**
	 * @param \WordPress\AsyncHttp\Request $request
	 */
	protected function enqueue_request( $request ) {
		$stream                       = StreamWrapper::create_resource(
			new StreamData( $request, $this )
		);
		$this->requests[ $request ]   = new RequestInfo( $stream );
		$this->queue_needs_processing = true;

		return $stream;
	}

	/**
	 * Starts n enqueued request up to the $concurrency_limit.
	 */
	public function process_queue() {
		$this->queue_needs_processing = false;

		$active_requests = count( $this->get_streamed_requests() );
		$backfill        = $this->concurrency - $active_requests;
		if ( $backfill <= 0 ) {
			return;
		}

		$enqueued                           = array_slice( $this->get_enqueued_request(), 0, $backfill );
		list( $streams, $response_headers ) = streams_send_http_requests( $enqueued );

		foreach ( $streams as $k => $stream ) {
			$request                            = $enqueued[ $k ];
			$total                              = $response_headers[ $k ]['headers']['content-length'];
			$this->requests[ $request ]->state  = RequestInfo::STATE_STREAMING;
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
		$enqueued_requests = array();
		foreach ( $this->requests as $request => $info ) {
			if ( $info->state === RequestInfo::STATE_ENQUEUED ) {
				$enqueued_requests[] = $request;
			}
		}

		return $enqueued_requests;
	}

	protected function get_streamed_requests() {
		$active_requests = array();
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
	 * @param Request $request
	 * @param $length
	 *
	 * @return false|string
	 * @throws Exception
	 */
	public function read_bytes( $request, $length ) {
		if ( ! isset( $this->requests[ $request ] ) ) {
			return false;
		}

		if ( $this->queue_needs_processing ) {
			$this->process_queue();
		}

		$request_info = $this->requests[ $request ];
		$stream       = $request_info->stream;

		$active_requests = $this->get_streamed_requests();
		$active_streams  = array_map(
			function ( $request ) {
				return $this->requests[ $request ]->stream;
			},
			$active_requests
		);

		if ( ! count( $active_streams ) ) {
			return false;
		}

		while ( true ) {
			if ( ! $request_info->is_finished() && feof( $stream ) ) {
				$request_info->state = RequestInfo::STATE_FINISHED;
				fclose( $stream );
				$this->queue_needs_processing = true;
			}

			if ( strlen( $request_info->buffer ) >= $length ) {
				$buffered             = substr( $request_info->buffer, 0, $length );
				$request_info->buffer = substr( $request_info->buffer, $length );

				return $buffered;
			} elseif ( $request_info->is_finished() ) {
				unset( $this->requests[ $request ] );

				return $request_info->buffer;
			}

			$active_streams = array_filter(
				$active_streams,
				function ( $stream ) {
					return ! feof( $stream );
				}
			);
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
