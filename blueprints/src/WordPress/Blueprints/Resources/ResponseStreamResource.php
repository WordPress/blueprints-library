<?php

namespace blueprints\src\WordPress\Blueprints\Resources;

use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class ResponseStreamResource implements Resource {

	private string $buffer = '';

	public function __construct(
		protected ResponseStreamInterface $stream
	) {
	}

	public function saveTo( string $path ): void {
		while ( $chunk = $this->read( 8192 ) ) {
			file_put_contents( $path, $chunk, FILE_APPEND );
		}
	}

	public function read( int $bytes ): string|bool {
		foreach ( $this->stream as $chunk ) {
			$this->buffer .= $chunk->getContent();
			if ( strlen( $this->buffer ) >= $bytes ) {
				$chunk        = substr( $this->buffer, 0, $bytes );
				$this->buffer = substr( $this->buffer, $bytes );

				return $chunk;
			}
		}

		return $this->buffer;
	}

	public function close(): void {
	}
}
