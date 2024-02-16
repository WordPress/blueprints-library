<?php

namespace WordPress\Blueprints;

class Map implements \ArrayAccess {
	private array $pairs = [];

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
		var_dump( $offset );
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

}
