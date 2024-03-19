<?php

namespace WordPress\Util;

use ArrayAccess;
use IteratorAggregate;
use Traversable;

class Map implements ArrayAccess, IteratorAggregate {
	private $pairs = [];

	public function __construct() {
	}

	public function offsetExists( $offset ): bool {
		foreach ( $this->pairs as $pair ) {
			if ( $pair[0] === $offset ) {
				return true;
			}
		}

		return false;
	}

	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		foreach ( $this->pairs as $pair ) {
			if ( $pair[0] === $offset ) {
				return $pair[1];
			}
		}

		// TODO Evaluate waring: 'ext-json' is missing in composer.json
		throw new \Exception( "Stream for resource " . json_encode( $offset ) . " not found" );
	}

	public function offsetSet( $offset, $value ) {
		foreach ( $this->pairs as $k => $pair ) {
			if ( $pair[0] === $offset ) {
				$this->pairs[ $k ] = [ $offset, $value ];

				return;
			}
		}
		$this->pairs[] = [ $offset, $value ];
	}

	public function offsetUnset( $offset ) {
		foreach ( $this->pairs as $i => $pair ) {
			if ( $pair[0] === $offset ) {
				unset( $this->pairs[ $i ] );
			}
		}
	}

	public function getIterator(): Traversable {
		return new ArrayPairIterator( array_values( $this->pairs ) );
	}
}
