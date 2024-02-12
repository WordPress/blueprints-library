<?php

namespace WordPress\Streams;

class StreamPeeker {
	private $position;
	private $stream;
	private $callback;
	private $onClose;

	protected $context;

	static public function register() {
		if ( ! stream_wrapper_register( 'peek', static::class ) ) {
			throw new \Exception( 'Failed to register protocol' );
		}
	}

	static public function unregister() {
		stream_wrapper_unregister( 'peek' );
	}

	static public function wrap( StreamPeekerData $data ) {
		$context = stream_context_create( [
			'peek' => [
				'wrapper_data' => $data,
			],
		] );

		return fopen( 'peek://', 'r', false, $context );
	}

	// Opens the stream
	public function stream_open( $path, $mode, $options, &$opened_path ) {
		$contextOptions = stream_context_get_options( $this->context );

		if ( isset( $contextOptions['peek']['wrapper_data']->fp ) ) {
			$this->stream = $contextOptions['peek']['wrapper_data']->fp;
		} else {
			return false;
		}

		if ( isset( $contextOptions['peek']['wrapper_data']->callback ) && is_callable( $contextOptions['peek']['wrapper_data']->callback ) ) {
			$this->callback = $contextOptions['peek']['wrapper_data']->callback;
		} else {
			// Default callback function if none provided
			$this->callback = function ( $data ) {
			};
		}

		if ( isset( $contextOptions['peek']['wrapper_data']->onClose ) && is_callable( $contextOptions['peek']['wrapper_data']->onClose ) ) {
			$this->onClose = $contextOptions['peek']['wrapper_data']->onClose;
		} else {
			// Default onClose function if none provided
			$this->onClose = function () {
			};
		}

		$this->position = 0;

		return true;
	}

	// Reads from the stream
	public function stream_read( $count ) {
		$ret            = fread( $this->stream, $count );
		$this->position += strlen( $ret );

		$callback = $this->callback;
		$callback( $ret );

		return $ret;
	}

	// Writes to the stream
	public function stream_write( $data ) {
		$written        = fwrite( $this->stream, $data );
		$this->position += $written;

		return $written;
	}

	// Closes the stream
	public function stream_close() {
		fclose( $this->stream );
		$onClose = $this->onClose;
		$onClose();
	}

	// Returns the current position of the stream
	public function stream_tell() {
		return $this->position;
	}

	// Checks if the end of the stream has been reached
	public function stream_eof() {
		return feof( $this->stream );
	}

	// Seeks to a specific position in the stream
	public function stream_seek( $offset, $whence ) {
		$result = fseek( $this->stream, $offset, $whence );
		if ( $result == 0 ) {
			$this->position = ftell( $this->stream );

			return true;
		} else {
			return false;
		}
	}

	// Stat information about the stream; providing dummy data
	public function stream_stat() {
		return [];
	}
}

StreamPeeker::register();
