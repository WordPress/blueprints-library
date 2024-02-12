<?php

namespace WordPress\Blueprints\Resources;

class StreamResource implements Resource {
	public function __construct(
		protected $stream
	) {
	}

	public function saveTo( string $path ): void {
		$fp = fopen( $path, 'w' );
		try {
			while ( true ) {
				if ( feof( $this->stream ) ) {
					break;
				}
				$data = fread( $this->stream, 8192 );
				var_dump( $data );
				if ( $data === false ) {
					break;
				}
				fwrite( $fp, $data );
			}
		} finally {
			fclose( $fp );
		}
	}

	public function read( int $bytes ): string|bool {
		return fread( $this->stream, $bytes );
	}

	public function close(): void {
		fclose( $this->stream );
	}
}
