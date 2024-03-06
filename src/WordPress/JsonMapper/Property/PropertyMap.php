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
		$this->map[ $property->get_name() ] = $property;
		$this->iterator                     = null;
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
		/** @var Property $property */
		foreach ( $other as $property ) {
			if ( ! $this->has_property( $property->get_name() ) ) {
				$this->addProperty( $property );
				continue;
			}

			if ( $property == $this->get_property( $property->get_name() ) ) {
				continue;
			}

			$current = $this->get_property( $property->get_name() );
			$builder = $current->as_builder();

			$builder->setIsNullable( $current->is_nullable() || $property->is_nullable() );
			foreach ( $property->get_property_types() as $propertyType ) {
				$builder->addType( $propertyType->getType(), $propertyType->getArrayInformation() );
			}

			$this->addProperty( $builder->build() );
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
