<?php

namespace WordPress\JsonMapper;

use ArrayObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use stdClass;

/**
 * Class JsonMapper
 *
 * This class is responsible for mapping JSON data to PHP class instances.
 * Custom factories can be provided for instantiating objects of specific classes.
 *
 * @package WordPress\JsonMapper
 */
class JsonMapper {
	/**
	 * Factories for instantiating specific classes.
	 *
	 * @var array<string,callable>
	 */
	private $factories = array();

	/**
	 * @param null|array $custom_factories A map of class name to an instance factory function.
	 *                                     The function takes a single stdClass argument with parsed
	 *                                     JSON data and returns a class instance.
	 */
	public function __construct( array $custom_factories = array() ) {
		$this->add_factories_for_native_php_classes();
		$this->add_custom_factories( $custom_factories );
	}

	/**
	 * Creates an instance of $class_name based on parsed JSON data.
	 *
	 * @param stdClass $json The JSON object containing the data to populate the new object with.
	 * @param string   $class_name The fully qualified name of the class to create an instance of.
	 *
	 * @return object An instance of the class specified by $class_name, populated with data from $json.
	 * @throws ReflectionException If the class does not exist and the instance is being created manually.
	 * @throws JsonMapperException If mapping the value to an associated property type failed or if setting was
	 *                             impossible.
	 */
	public function hydrate( $json, $class_name ) {
		return $this->has_factory( $class_name )
			? $this->run_factory( $class_name, $json )
			: $this->create_and_hydrate( $class_name, $json );
	}

	/**
	 * Populates an instance of $class_name created via ReflectionClass::newInstance.
	 *
	 * @param stdClass $json The JSON object containing the data to populate the new object with.
	 * @param string   $class_name The fully qualified name of the class to create an instance of.
	 *
	 * @return object An instance of the class specified by $class_name, populated with data from $json.
	 * @throws ReflectionException If the class does not exist.
	 * @throws JsonMapperException If mapping the value to an associated property type failed or if setting was
	 *                             impossible.
	 */
	private function create_and_hydrate( string $class_name, stdClass $json ) {
		$reflection_class = new ReflectionClass( $class_name );
		$object           = $reflection_class->newInstance();
		$property_map     = PropertyParser::compute_property_map( $reflection_class );

		foreach ( (array) $json as $key => $value ) {
			// Ignore null data in JSON.
			if ( null === $value ) {
				continue;
			}

			$property = $property_map[ $key ] ?? null;
			if ( null === $property ) {
				continue;
			}

			$value = $this->map_value( $property, $value );
			$this->set_value( $object, $property, $value );
		}

		return $object;
	}

	/**
	 * Maps a value from the JSON object to the type of the given Property.
	 *
	 * Warning - array depths are not fully checked during mapping.
	 *
	 * @param Property $property The Property object with a list of possible types the value could be mapped to.
	 * @param mixed    $value The value from the JSON object.
	 *
	 * @return mixed The mapped value, of the type specified by the Property.
	 * @throws JsonMapperException If the Property type is not supported, or if the value cannot be mapped to the Property type.
	 */
	private function map_value( Property $property, $value ) {
		if ( 0 === count( $property->property_types ) ) {
			// Return the value as is - there is no type info.
			return $value;
		}

		foreach ( $property->property_types as $property_type ) {
			$array_dimensions = PropertyParser::get_array_dimensions( $property_type );
			$property_type    = PropertyParser::without_dimensions( $property_type );
			$type_is_array    = 'array' === $property_type || $array_dimensions > 0;

			if ( is_array( $value ) && $type_is_array && count( $value ) === 0 ) {
				return array();
			}

			if ( $this->is_scalar_recursive( $value, $property_type ) ) {
				return $this->map_to_scalar_recursive( $value, $property_type );
			}

			if ( $this->has_factory( $property_type ) ) {
				return $this->map_using_factory( $value, $property_type );
			}

			if ( class_exists( $property_type ) || interface_exists( $property_type ) ) {
				return $this->map_to_object( $value, $property_type );
			}
		}

		// If nothing more precise worked, the value is an array, and it can be mapped to an array type,
		// just return the values as is. This ignores the array dimensions.
		// @TODO: Take array dimensions into account.
		if ( is_array( $value ) ) {
			foreach ( $property->property_types as $property_type ) {
				if ( preg_match( '/^(array|mixed|object)(\[\])*$/', $property_type ) ) {
					return $value;
				}
			}
		}

		throw new JsonMapperException(
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'Unable to map ' . json_encode( $value ) . " to '$property->name'."
		);
	}


	private function set_value( $object, Property $property, $value ) {
		// Use a setter if it exists.
		$method_name = 'set' . ucfirst( $property->name );
		if ( method_exists( $object, $method_name ) ) {
			$method     = new ReflectionMethod( $object, $method_name );
			$parameters = $method->getParameters();

			if ( is_array( $value ) && count( $parameters ) === 1 && $parameters[0]->isVariadic() ) {
				call_user_func_array( array( $object, $method_name ), $value );

				return;
			}

			$object->$method_name( $value );

			return;
		}

		// Use a public property if it exists.
		if ( 'public' === $property->visibility ) {
			$object->{$property->name} = $value;

			return;
		}

		throw new JsonMapperException(
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			"Property: '" . get_class( $object ) . "::$property->name' is non-public and no setter method was found."
		);
	}

	private function is_scalar_recursive( $value, string $property_type ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $inner_value ) {
				if ( ! $this->is_scalar_recursive( $inner_value, $property_type ) ) {
					return false;
				}
			}

			return true;
		}

		if ( ! is_scalar( $value ) || ! Utils::is_type_scalar( $property_type ) ) {
			return false;
		}

		$value_type = gettype( $value );

		if ( 'boolean' === $value_type ) {
			return 'boolean' === $property_type || 'bool' === $property_type;
		}

		if ( 'integer' === $value_type ) {
			return 'integer' === $property_type || 'int' === $property_type;
		}

		if ( 'double' === $value_type ) {
			return 'float' === $property_type || 'double' === $property_type;
		}

		return $value_type === $property_type;
	}

	private function map_to_scalar_recursive( $value, string $property_type ) {
		if ( is_array( $value ) ) {
			$mapped = array();
			foreach ( $value as $inner_value ) {
				$mapped[] = $this->map_to_scalar_recursive( $inner_value, $property_type );
			}

			return $mapped;
		}

		if ( 'string' === $property_type ) {
			return (string) $value;
		}
		if ( 'boolean' === $property_type || 'bool' === $property_type ) {
			return (bool) $value;
		}
		if ( 'integer' === $property_type || 'int' === $property_type ) {
			return (int) $value;
		}
		if ( 'double' === $property_type || 'float' === $property_type ) {
			return (float) $value;
		}

		throw new \InvalidArgumentException( "\'$property_type\' is not a scalar value so it could not be cast to a scalar type." );
	}

	private function map_using_factory( $value, string $property_type ) {
		if ( is_array( $value ) ) {
			$mapped = array();
			foreach ( $value as $inner_value ) {
				$mapped[] = $this->map_using_factory( $inner_value, $property_type );
			}

			return $mapped;
		}

		return $this->run_factory( $property_type, $value );
	}

	private function map_to_object( $value, string $property_type ) {
		if ( ! ( new ReflectionClass( $property_type ) )->isInstantiable() ) {
			// phpcs:ignore
			throw new JsonMapperException( "Unable to resolve uninstantiable \'$property_type\'." );
		}

		if ( is_array( $value ) ) {
			$mapped = array();
			foreach ( $value as $inner_value ) {
				$mapped[] = $this->map_to_object( $inner_value, $property_type );
			}

			return $mapped;
		}

		return $this->hydrate( $value, $property_type );
	}

	private function sanitize_class_name( string $class_name ): string {
		/* Erase leading slash as ::class doesn't contain leading slash */
		if ( strpos( $class_name, '\\' ) === 0 ) {
			$class_name = substr( $class_name, 1 );
		}

		return $class_name;
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
				return new ArrayObject( $value );
			}
		);
	}

	private function add_custom_factories( array $custom_factories ) {
		foreach ( $custom_factories as $class_name => $custom_factory ) {
			$this->add_factory( $class_name, $custom_factory );
		}
	}

	private function has_factory( string $class_name ): bool {
		return array_key_exists( $this->sanitize_class_name( $class_name ), $this->factories );
	}

	private function run_factory( string $class_name, $params ) {
		return $this->factories[ $this->sanitize_class_name( $class_name ) ]( $params );
	}

	private function add_factory( string $class_name, callable $factory ) {
		$this->factories[ $this->sanitize_class_name( $class_name ) ] = $factory;
	}
}
