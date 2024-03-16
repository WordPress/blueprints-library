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
 * This class is responsible for mapping JSON data to PHP objects. It supports mapping to native PHP classes
 * as well as custom classes. Custom factories can be provided for instantiating objects of specific classes.
 *
 * @package WordPress\JsonMapper
 */
class JsonMapper {
	const ARRAY_TYPE       = '/^array(\[])*$/';
	const MIXED_ARRAY_TYPE = '/^mixed(\[])+$/';
	/**
	 * Array of strings representing valid scalar types.
	 *
	 * @var string[]
	 */
	private $scalar_types = array( 'string', 'bool', 'boolean', 'int', 'integer', 'double', 'float' );

	/**
	 * Array of factories for instantiating objects of specific classes.
	 *
	 * @var array<string,callable>
	 */
	private $factories = array();

	/**
	 * Constructs a new instance of this class.
	 *
	 * This constructor initializes the object and adds factories for native PHP classes.
	 * It also allows for the addition of custom factories through the $custom_factories parameter.
	 *
	 * @param null|array $custom_factories An associative array where the key is the class name
	 * and the value is a callable factory function. The factory function is expected to take
	 * an instance of stdClass as its argument and return an instance of the class specified
	 * by the key. This parameter is optional, and if not provided, an empty array will be used.
	 */
	public function __construct( array $custom_factories = array() ) {
		$this->add_factories_for_native_php_classes();
		$this->add_custom_factories( $custom_factories );
	}

	/**
	 * Creates an instance of the given class and populates it with data from the given JSON object.
	 *
	 * This method first checks if a factory exists for the given class. If a factory exists, it uses the factory
	 * to create the instance and populate it with data. If no factory exists, it creates the instance and populates
	 * it manually by calling the `hydrate_manually` method.
	 *
	 * @param stdClass $json The JSON object containing the data to populate the new object with.
	 * @param string   $class_name The fully qualified name of the class to create an instance of.
	 * @return object An instance of the class specified by $class_name, populated with data from $json.
	 * @throws ReflectionException If the class does not exist and the instance is being created manually.
	 * @throws JsonMapperException If mapping the value to an associated property type failed or if setting was
	 * impossible.
	 */
	public function hydrate( stdClass $json, string $class_name ) {
		return $this->has_factory( $class_name )
			? $this->use_factory( $class_name, $json )
			: $this->hydrate_manually( $json, $class_name );
	}

	/**
	 * Creates an instance of the given class and populates its properties with data from the given JSON object.
	 *
	 * This method uses PHP's Reflection API to create a new instance of the class specified by $class_name.
	 * It then uses the PropertyParser to compute a property map for the class, which is an associative array
	 * where the keys are property names and the values are {@link Property} objects.
	 *
	 * The method then iterates over the properties of the JSON object. For each property, it retrieves the
	 * corresponding Property object from the property map, maps the JSON value to the type of the Property,
	 * and sets the value of the Property on the newly created object.
	 *
	 * If the JSON object contains a property that is not defined in the class, or if the value of a property
	 * in the JSON object is null, that property is ignored.
	 *
	 * @param stdClass $json The JSON object containing the data to populate the new object with.
	 * @param string   $class_name The fully qualified name of the class to create an instance of.
	 * @return object An instance of the class specified by $class_name, populated with data from $json.
	 * @throws ReflectionException If the class does not exist.
	 * @throws JsonMapperException If mapping the value to an associated property type failed or if setting was
	 * impossible.
	 */
	private function hydrate_manually( stdClass $json, string $class_name ) {
		$reflection_class = new ReflectionClass( $class_name );
		$object           = $reflection_class->newInstance();
		$property_map     = PropertyParser::compute_property_map( $reflection_class );

		foreach ( (array) $json as $value_name => $value ) {
			// Ignore null data in JSON.
			if ( null === $value ) {
				continue;
			}

			$property = self::get_property_for_value( $value_name, $property_map );
			// Ignore additional data in JSON.
			if ( null === $property ) {
				continue;
			}

			$value = $this->map_value( $property, $value );
			$this->set_value( $object, $property, $value );
		}

		return $object;
	}

	/**
	 * Retrieves the Property object associated with a given value name from a property map.
	 *
	 * This method iterates over the provided property map and returns the Property object
	 * whose name matches the provided value name. If no matching Property is found, it returns null.
	 *
	 * @param string     $value_name The name of the value for which to find the corresponding Property.
	 * @param Property[] $property_map An associative array where the keys are property names
	 * and the values are Property objects.
	 * @return Property|null The Property object associated with the given value name,
	 * or null if no matching Property is found.
	 */
	private static function get_property_for_value( string $value_name, array $property_map ) {
		foreach ( $property_map as $property_name => $property ) {
			if ( $property_name === $value_name ) {
				return $property;
			}
		}
		return null;
	}


	private function map_value( Property $property, $value ) {
		if ( 0 === count( $property->property_types ) ) {
			// Return the value as is - there is no type info.
			return $value;
		}

		foreach ( $property->property_types as $property_type ) {
			$array_depth   = substr_count( $property_type, '[]' );
			$property_type = str_replace( '[]', '', $property_type );
			$is_array      = 'array' === $property_type || $array_depth > 0;

			if ( is_array( $value ) && $is_array && count( $value ) === 0 ) {
				return array();
			}

			if ( $this->is_property_and_value_same_scalar( $property_type, $value ) ) {
				return $this->map_to_scalar( $property_type, $value );
			}

			if ( $this->has_factory( $property_type ) ) {
				return $this->map_to_object_using_factory( $property_type, $value );
			}

			if ( ( class_exists( $property_type ) || interface_exists( $property_type ) ) ) {
				return $this->map_to_object( $property_type, $value );
			}
		}

		// If nothing more precise worked, value is an array, and one of the types is an array or mixed[], try it.
		// Will work for deeper arrays. Does not check if depth matches.
		if ( is_array( $value )
			&& ( $this->is_matching_property( $property->property_types, self::ARRAY_TYPE ) )
			|| $this->is_matching_property( $property->property_types, self::MIXED_ARRAY_TYPE ) ) {
			return $value;
		}

		throw new JsonMapperException(
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'Unable to map ' . json_encode( $value ) . " to '$property->name'."
		);
	}

	private function is_matching_property( $property_types, $pattern ) {
		foreach ( $property_types as $property_type ) {
			if ( preg_match( $pattern, $property_type ) ) {
				return true;
			}
		}
		return false;
	}

	private function set_value( $object, Property $property, $value ) {
		if ( 'public' === $property->visibility ) {
			$object->{$property->name} = $value;
			return;
		}

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

		throw new JsonMapperException(
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			"Property: '" . get_class( $object ) . "::$property->name' is non-public and no setter method was found."
		);
	}

	private function is_property_and_value_same_scalar( string $property_type, $value ) {
		if ( false === is_array( $value ) ) {
			if ( false === is_scalar( $value ) || false === $this->is_valid_scalar_type( $property_type ) ) {
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

		foreach ( $value as $inner_value ) {
			if ( false === $this->is_property_and_value_same_scalar( $property_type, $inner_value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param string $property_type
	 * @return bool
	 */
	private function is_valid_scalar_type( string $property_type ): bool {
		return in_array( $property_type, $this->scalar_types, true );
	}

	private static function cast_to_scalar_type( string $type, $value ) {
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

	private function map_to_scalar( string $property_type, $value ) {
		if ( false === is_array( $value ) ) {
			return self::cast_to_scalar_type( $property_type, $value );
		}
		$mapped = array();
		foreach ( $value as $inner_value ) {
			$mapped[] = $this->map_to_scalar( $property_type, $inner_value );
		}
		return $mapped;
	}

	private function map_to_object_using_factory( string $property_type, $value ) {
		if ( false === is_array( $value ) ) {
			return $this->use_factory( $property_type, $value );
		}
		$mapped = array();
		foreach ( $value as $inner_value ) {
			$mapped[] = $this->map_to_object_using_factory( $property_type, $inner_value );
		}
		return $mapped;
	}

	private function map_to_object( string $property_type, $value ) {
		if ( false === ( new ReflectionClass( $property_type ) )->isInstantiable() ) {
			// phpcs:ignore
			throw new JsonMapperException( "Unable to resolve uninstantiable \'$property_type\'." );
		}
		if ( false === is_array( $value ) ) {
			return $this->hydrate( $value, $property_type );
		}
		$mapped = array();
		foreach ( $value as $inner_value ) {
			$mapped[] = $this->map_to_object( $property_type, $inner_value );
		}
		return $mapped;
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
			ArrayObject::class,
			function ( $value ) {
				return new ArrayObject( $value );
			}
		);
	}
}
