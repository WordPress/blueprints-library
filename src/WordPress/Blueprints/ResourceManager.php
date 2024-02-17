<?php

namespace WordPress\Blueprints;

use Symfony\Component\Filesystem\Filesystem;
use function Playground\temp_path;

class ResourceManager implements \ArrayAccess {
	private array $pairs = [];

	private Filesystem $fs;

	public function __construct() {
		$this->fs = new Filesystem();
	}

	public function offsetExists( $offset ): bool {
		foreach ( $this->pairs as $pair ) {
			if ( $pair[0] === $offset ) {
				return true;
			}
		}
	}

	public function offsetGet( $offset ): mixed {
		foreach ( $this->pairs as $pair ) {
			if ( $pair[0] === $offset ) {
				return $pair[1];
			}
		}
		throw new \Exception( "Offset not found" );
	}

	public function offsetSet( $offset, $value ): void {
		foreach ( $this->pairs as $k => $pair ) {
			if ( $pair[0] === $offset ) {
				$this->pairs[ $k ] = [ $offset, $value ];

				return;
			}
		}
		$this->pairs[] = [ $offset, $value ];
	}

	public function offsetUnset( $offset ): void {
		foreach ( $this->pairs as $i => $pair ) {
			if ( $pair[0] === $offset ) {
				unset( $this->pairs[ $i ] );
			}
		}
	}

	public function bufferToTemporaryFile( $offset, $callback, $suffix = null ) {
		$fp   = $this[ $offset ];
		$path = $this->fs->tempnam( sys_get_temp_dir(), 'resource', $suffix );
		$this->fs->dumpFile( $path, $fp );

		try {
			return $callback( $path );
		} finally {
			$this->fs->remove( $path );
		}
	}

}
