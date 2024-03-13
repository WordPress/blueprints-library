<?php

namespace WordPress\JsonMapper;

use ReflectionClass;
use ReflectionMethod;
use stdClass;

class JsonMapper {
	private $scalar_types = array( 'string', 'bool', 'boolean', 'int', 'integer', 'double', 'float' );

	/**
	 * @var array{$class_name}
	 */
	private $factories = array();

	public function __construct( array $custom_factories = array() ) {
		$this->add_factories_for_native_php_classes();
		$this->add_custom_factories( $custom_factories );
	}

	/**
	 * @param stdClass $json
	 * @param string $class_name
	 * @return object
	 * @throws JsonMapperException
	 */
	public function hydrate( stdClass $json, string $class_name ) {
		$object_wrapper = new ObjectWrapper( null, $class_name );

		$property_map = DocBlockAnnotations::compute_property_map( $object_wrapper );

		$this->map_json_to_object( $json, $object_wrapper, $property_map );

		return $object_wrapper->getObject();
	}

	/**
	 * @param stdClass $json
	 * @param ObjectWrapper $object_wrapper
	 * @param Property[] $property_map
	 * @return void
	 * @throws JsonMapperException
	 */
	private function map_json_to_object( stdClass $json, ObjectWrapper $object_wrapper, array $property_map ) {
		if ( $this->has_factory( $object_wrapper->getName() ) ) {
			$result = $this->use_factory( $object_wrapper->getName(), $json );

			$object_wrapper->setObject( $result );
			return;
		}

		$values = (array) $json;
		foreach ( $values as $key => $value ) {
			if ( null === $property = self::get_property( $property_map, $key ) ) {
				continue;
			}
			if ( false === $property->is_nullable && null === $value ) {
				throw new JsonMapperException(
					"Null provided in json where \'{$object_wrapper->getName()}::{$key}\' doesn't allow null value"
				);
			}

			if ( $property->is_nullable && null === $value ) {
				$this->set_value( $object_wrapper, $property, null );
				continue;
			}

			$value = $this->map_value( $property, $value );
			$this->set_value( $object_wrapper, $property, $value );
		}
	}

	private function map_value( Property $property, $value ) {
		if ( null === $value && $property->is_nullable ) {
			return null;
		}
		// No match was found (or there was only one option) lets assume the first is the right one.
		$types = $property->property_types;
		$property_type  = \array_shift( $types );

		if ( null === $property_type ) {
			// Return the value as is as there is no type info.
			return $value;
		}

		if ( $this->is_valid_scalar_type( $property_type ) ) {
			return $this->map_to_scalar( $property_type, $value );
		}

		if ( $this->has_factory( $property_type ) ) {
			return $this->map_to_object_using_factory( $property_type, $value );
		}

		if ( ( class_exists( $property_type ) || interface_exists( $property_type ) ) ) {
			return $this->map_to_object( $property_type, $value );
		}

		throw new JsonMapperException( "Unable to map to \'{$property_type}\'" );
	}

	/**
	 * @param string $property_type
	 * @return bool
	 */
	private function is_valid_scalar_type( string $property_type ): bool {
		return in_array( $property_type, $this->scalar_types, true );
	}

	private function map_to_scalar(string $property_type, $value ) {
		if ( false === is_array( $value ) ) {
			return $this->cast_to_scalar_type( $property_type, $value );
		}
		$mapped_scalars = array();
		foreach ( $value as $inner_value ) {
			$mapped_scalars[] = $this->map_to_scalar( $property_type, $inner_value );
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

	private function map_to_object_using_factory( string $property_type, $value ): array{
		if ( false === is_array( $value ) ) {
			return $this->use_factory( $property_type, $value );
		}
		$mapped_objects = array();
		foreach ( $value as $inner_value ) {
			$mapped_objects[] = $this->map_to_object_using_factory( $property_type, $inner_value );
		}
		return $mapped_objects;
	}

	private function map_to_object(string $property_type, $value ) {
		if ( false === ( new ReflectionClass( $property_type ) )->isInstantiable() ) {
			throw new JsonMapperException( "Unable to resolve uninstantiable \'{$property_type}\'." );
		}
		if ( false === is_array( $value ) ) {
			return $this->hydrate( $value, $property_type );
		}
		$mapped_objects = array();
		foreach ( $value as $inner_value ) {
			$mapped_objects[] = $this->map_to_object( $property_type, $inner_value );
		}
		return $mapped_objects;
	}

	private function set_value( ObjectWrapper $object, Property $property, $value ) {
		if ( 'public' === $property->visibility ) {
			$object->getObject()->{$property->name} = $value;
			return;
		}

		$method_name = 'set' . \ucfirst( $property->name );
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
			"{$object->getName()}::{$property->name} is non-public and no setter method was found"
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

	private function add_custom_factories( array $custom_factories ) {
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
		$this->add_factory(
			\ArrayObject::class,
			function ( $value ) {
				return new \ArrayObject( $value );
			}
		);
	}

	/**
	 * @param array $property_map
	 * @param string $property_name
	 * @return null|Property
	 */
	private static function get_property ( array $property_map, string $property_name ) {
		foreach ( $property_map as $property ) {
			if ( $property->name === $property_name ) {
				return $property;
			}
		}
		return null;
	}
}
