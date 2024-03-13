<?php

namespace WordPress\JsonMapper\Property;

use ArrayIterator;
use function array_key_exists;

class PropertyMap implements \IteratorAggregate, \JsonSerializable {
	/** @var Property[] */
	private $map = array();

	/** @var ArrayIterator*/
	private $iterator = null;

	public function __construct() {}

	public function addProperty( Property $property ) {
		$this->map[ $property->name ] = $property;
		$this->iterator               = null;
	}

	public function has_property( string $name ): bool {
		return array_key_exists( $name, $this->map );
	}

	public function get_property( string $key ): Property {
		if ( false === $this->has_property( $key ) ) {
			throw new JsonMapperException( "There is no property named $key" );
		}

		return $this->map[ $key ];
	}

	public function merge( self $other ) {
		/** @var Property $other_property */
		foreach ( $other as $other_property ) {
			$other_name = $other_property->name;
			if ( false === $this->has_property( $other_name ) ) {
				$this->addProperty( $other_property );
				continue;
			}

			if ( $other_property === $this->get_property( $other_name ) ) {
				continue;
			}

			$property                 = $this->get_property( $other_name );
			$property->is_nullable    = $property->is_nullable || $other_property->is_nullable;
			$property->property_types = array_merge( $property->property_types, $other_property->property_types );
			$this->addProperty( $property );
		}
		$this->iterator = null;
	}

	// phpcs:ignore
	public function getIterator(): ArrayIterator {
		if ( \is_null( $this->iterator ) ) {
			$this->iterator = new ArrayIterator( $this->map );
		}

		return $this->iterator;
	}

	// phpcs:ignore
	public function jsonSerialize(): array {
		return array(
			'properties' => $this->map,
		);
	}

	public function toString(): string {
		return (string) \json_encode( $this );
	}
}
