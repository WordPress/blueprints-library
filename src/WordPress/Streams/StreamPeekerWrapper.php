<?php

namespace WordPress\Streams;

class StreamPeekerWrapper extends VanillaStreamWrapper {
	protected $onChunk;
	protected $onClose;
	protected $position;

	const SCHEME = 'peek';

	// Opens the stream
	public function stream_open( $path, $mode, $options, &$opened_path ) {
		parent::stream_open( $path, $mode, $options, $opened_path );

		if ( isset( $this->wrapper_data->fp ) ) {
			$this->stream = $this->wrapper_data->fp;
		} else {
			return false;
		}

		if ( isset( $this->wrapper_data->onChunk ) && is_callable( $this->wrapper_data->onChunk ) ) {
			$this->onChunk = $this->wrapper_data->onChunk;
		} else {
			// Default onChunk function if none provided
			$this->onChunk = function ( $data ) {
			};
		}

		if ( isset( $this->wrapper_data->onClose ) && is_callable( $this->wrapper_data->onClose ) ) {
			$this->onClose = $this->wrapper_data->onClose;
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
		$ret             = fread( $this->stream, $count );
		$this->position += strlen( $ret );

		$onChunk = $this->onChunk;
		$onChunk( $ret );

		return $ret;
	}

	// Writes to the stream
	public function stream_write( $data ) {
		$written         = fwrite( $this->stream, $data );
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
}
