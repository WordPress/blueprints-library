<?php

namespace WordPress\JsonMapper;

use stdClass;
use WordPress\JsonMapper\Property\Property;
use WordPress\JsonMapper\Property\PropertyMap;
use WordPress\JsonMapper\Property\PropertyType;

class JsonMapper {
	private $scalar_types = array( 'string', 'bool', 'boolean', 'int', 'integer', 'double', 'float' );

	private $property_mappers;

	private $factories = array();

	public function __construct( array $property_mappers = array(), array $custom_factories = array() ) {
		$this->property_mappers = $property_mappers;
		$this->add_factories_for_native_php_classes();
		$this->add_factory_for_arrays();
		$this->add_custom_factories( $custom_factories );
	}

	public function hydrate( stdClass $json, string $target ) {
		$object_wrapper = new ObjectWrapper( null, $target );
		$property_map   = new PropertyMap();

		foreach ( $this->property_mappers as $property_mapper ) {
			$property_mapper->map_properties( $object_wrapper, $property_map );
		}

		$this->map_json_to_object( $json, $object_wrapper, $property_map );

		return $object_wrapper->getObject();
	}

	public function map_json_to_object(stdClass $json, ObjectWrapper $object_wrapper, PropertyMap $property_map ) {
		// If the type we are mapping has a last minute factory use it.
		if ( $this->has_factory( $object_wrapper->getName() ) ) {
			$result = $this->use_factory( $object_wrapper->getName(), $json );

			$object_wrapper->setObject( $result );
			return;
		}

		$values = (array) $json;
		foreach ( $values as $key => $value ) {
			if ( false === $property_map->has_property( $key ) ) {
				continue;
			}

			$property = $property_map->get_property( $key );

			if ( false === $property->is_nullable() && null === $value ) {
				throw new JsonMapperException(
					"Null provided in json where \'{$object_wrapper->getName()}::{$key}\' doesn't allow null value"
				);
			}

			if ( $property->is_nullable() && null === $value ) {
				$this->set_value( $object_wrapper, $property, null );
				continue;
			}

			$value = $this->map_value( $property, $value );
			$this->set_value( $object_wrapper, $property, $value );
		}
	}

	private function map_value( Property $property, $value ) {
		if ( null === $value && $property->is_nullable() ) {
			return null;
		}
		// No match was found (or there was only one option) lets assume the first is the right one.
		$types = $property->get_property_types();
		$type  = \array_shift( $types );

		if ( null === $type ) {
			// Return the value as is as there is no type info.
			return $value;
		}

		if ( $this->is_valid_scalar_type( $type ) ) {
			return $this->map_to_scalar( $type, $value );
		}

		if ( $this->has_factory( $type->getType() ) ) {
			return $this->map_to_object_using_factory( $type, $value );
		}

		if ( ( class_exists( $type->getType() ) || interface_exists( $type->getType() ) ) ) {
			return $this->map_to_object( $type, $value );
		}

		throw new JsonMapperException( "Unable to map to \'{$type->getType()}\'" );
	}

	/**
	 * @param PropertyType $type
	 * @return bool
	 */
	private function is_valid_scalar_type( PropertyType $type ): bool {
		return in_array( $type->getType(), $this->scalar_types, true );
	}

	private function map_to_scalar( PropertyType $type, $value ) {
		if ( false === is_array( $value ) ) {
			return $this->cast_to_scalar_type( $type->getType(), $value );
		}
		$mapped_scalars = array();
		foreach ( $value as $inner_value ) {
			$mapped_scalars[] = $this->map_to_scalar( $type, $inner_value );
		}
		return $mapped_scalars;
	}

	private function cast_to_scalar_type( string $type, $value ) {
		if ( 'string' === $type ) {
			return (string) $value;
		}
		if ( 'boolean' === $type || 'bool' === $type ) {
			return (bool) $value;
		}
		if ( 'integer' === $type || 'int' === $type ) {
			return (int) $value;
		}
		if ( 'double' === $type || 'float' === $type ) {
			return (float) $value;
		}

		throw new JsonMapperException( "Casting to scalar type \'$type\' failed." );
	}

	private function map_to_object_using_factory( PropertyType $type, $value ) {
		if ( false === is_array( $value ) ) {
			return $this->use_factory( $type->getType(), $value );
		}
		$mapped_objects = array();
		foreach ( $value as $inner_value ) {
			$mapped_objects[] = $this->map_to_object_using_factory( $type, $inner_value );
		}
		return $mapped_objects;
	}

	private function map_to_object( PropertyType $type, $value ) {
		if ( false === ( new ReflectionClass( $type->getType() ) )->isInstantiable() ) {
			throw new JsonMapperException( "Unable to resolve uninstantiable \'{$type->getType()}\'." );
		}
		if ( false === is_array( $value ) ) {
			return $this->mapper->hydrate( $value, $type->getType() );
		}
		$mapped_objects = array();
		foreach ( $value as $inner_value ) {
			$mapped_objects[] = $this->map_to_object( $type, $inner_value );
		}
		return $mapped_objects;
	}

	private function set_value( ObjectWrapper $object, Property $property, $value ) {
		if ( 'public' === $property->visibility ) {
			$object->getObject()->{$property->get_name()} = $value;
			return;
		}

		$method_name = 'set' . \ucfirst( $property->get_name() );
		if ( \method_exists( $object->getObject(), $method_name ) ) {
			$method     = new ReflectionMethod( $object->getObject(), $method_name );
			$parameters = $method->getParameters();

			if ( \is_array( $value ) && \count( $parameters ) === 1 && $parameters[0]->isVariadic() ) {
				$object->getObject()->$method_name( ...$value );
				return;
			}

			$object->getObject()->$method_name( $value );
			return;
		}

		throw new JsonMapperException(
			"{$object->getName()}::{$property->get_name()} is non-public and no setter method was found"
		);
	}

	private function sanitise_class_name( string $class_name ): string {
		/* Erase leading slash as ::class doesn't contain leading slash */
		if ( strpos( $class_name, '\\' ) === 0 ) {
			$class_name = substr( $class_name, 1 );
		}

		return $class_name;
	}

	private function has_factory( string $class_name ): bool {
		return array_key_exists( $this->sanitise_class_name( $class_name ), $this->factories );
	}

	private function use_factory( string $class_name, $params ) {
		$factory = $this->factories[ $this->sanitise_class_name( $class_name ) ];
		return $factory( $params );
	}

	private function add_factory( string $class_name, callable $factory ) {
		$this->factories[ $this->sanitise_class_name( $class_name ) ] = $factory;
	}

	public function add_custom_factories( array $custom_factories ) {
		foreach ( $custom_factories as $class_name => $custom_factory ) {
			$this->add_factory( $class_name, $custom_factory );
		}
	}

	private function add_factories_for_native_php_classes() {
		$this->add_factory(
			\DateTime::class,
			static function ( string $value ) {
				return new \DateTime( $value );
			}
		);
		$this->add_factory(
			\DateTimeImmutable::class,
			static function ( string $value ) {
				return new \DateTimeImmutable( $value );
			}
		);
		$this->add_factory(
			stdClass::class,
			static function ( $value ) {
				return (object) $value;
			}
		);
	}

	private function add_factory_for_arrays() {
		$this->add_factory(
			\ArrayObject::class,
			function ( $value ) {
				return new \ArrayObject( $value );
			}
		);
	}
}