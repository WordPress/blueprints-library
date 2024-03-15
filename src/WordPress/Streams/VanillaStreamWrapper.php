<?php

namespace WordPress\Streams;

class VanillaStreamWrapper implements StreamWrapperInterface {
	protected $stream;

	protected $context;

	protected $wrapper_data;

	const SCHEME = 'vanilla';

	static public function create_resource( VanillaStreamWrapperData $data ) {
		static::register();

		$context = stream_context_create( [
			static::SCHEME => [
				'wrapper_data' => $data,
			],
		] );

		return fopen( static::SCHEME . '://', 'r', false, $context );
	}

	static public function register() {
		if ( in_array( static::SCHEME, stream_get_wrappers() ) ) {
			return;
		}

		if ( ! stream_wrapper_register( static::SCHEME, static::class ) ) {
			throw new \Exception( 'Failed to register protocol' );
		}
	}

	static public function unregister() {
		stream_wrapper_unregister( 'async' );
	}


	public function stream_set_option( int $option, int $arg1, ?int $arg2 ): bool {
		if ( \STREAM_OPTION_BLOCKING === $option ) {
			return stream_set_blocking( $this->stream, (bool) $arg1 );
		} elseif ( \STREAM_OPTION_READ_TIMEOUT === $option ) {
			return stream_set_timeout( $this->stream, $arg1, $arg2 );
		}

		return false;
	}

	public function stream_open( $path, $mode, $options, &$opened_path ) {
		$contextOptions = stream_context_get_options( $this->context );

		if ( ! isset( $contextOptions[ static::SCHEME ]['wrapper_data'] ) || ! is_object( $contextOptions[ static::SCHEME ]['wrapper_data'] ) ) {
			return false;
		}

		$this->wrapper_data = $contextOptions[ static::SCHEME ]['wrapper_data'];

		if ( $this->wrapper_data->fp ) {
			$this->stream = $this->wrapper_data->fp;
		}

		return true;
	}

	public function stream_cast( int $cast_as ) {
		return $this->stream;
	}

	public function stream_read( $count ) {
		return fread( $this->stream, $count );
	}

	public function stream_write( $data ) {
		return fwrite( $this->stream, $data );
	}

	public function stream_close() {
		return fclose( $this->stream );
	}

	public function stream_tell() {
		return ftell( $this->stream );
	}

	public function stream_eof() {
		return feof( $this->stream );
	}

	public function stream_seek( $offset, $whence ) {
		return fseek( $this->stream, $offset, $whence );
	}

	public function stream_stat() {
		return [];
	}
}
