<?php

namespace WordPress\Streams;

interface StreamWrapperInterface {

	/**
	 * Sets an option on the stream
	 *
	 * @param int $option
	 * @param int $arg1
	 * @param int|null $arg2
	 *
	 * @return bool
	 */
	public function stream_set_option( int $option, int $arg1, ?int $arg2 ): bool;

	/**
	 * Opens the stream
	 */
	public function stream_open( $path, $mode, $options, &$opened_path );

	public function stream_cast( int $cast_as );

	/**
	 * Reads from the stream
	 */
	public function stream_read( $count );

	/**
	 * Writes to the stream
	 */
	public function stream_write( $data );

	/**
	 * Closes the stream
	 */
	public function stream_close();

	/**
	 * Returns the current position of the stream
	 */
	public function stream_tell();

	/**
	 * Checks if the end of the stream has been reached
	 */
	public function stream_eof();

	/**
	 * Seeks to a specific position in the stream
	 */
	public function stream_seek( $offset, $whence );

	/**
	 * Stat information about the stream; providing dummy data
	 */
	public function stream_stat();
}
