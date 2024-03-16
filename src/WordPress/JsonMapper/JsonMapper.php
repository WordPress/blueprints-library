<?php

namespace WordPress\JsonMapper;

use ArrayObject;
use ReflectionClass;
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

	public function hydrate( stdClass $json, string $class_name ) {
		return $this->has_factory( $class_name )
			? $this->use_factory( $class_name, $json )
			: $this->hydrate_manually( $class_name, $json );
	}

	private function hydrate_manually( string $class_name, stdClass $json ) {
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

	private function map_value( Property $property, $value ) {
		if ( 0 === count( $property->property_types ) ) {
			// Return the value as is - there is no type info.
			return $value;
		}

		foreach ( $property->property_types as $property_type ) {
			$array_depth   = substr_count( $property_type, '[]' );
			$is_array      = 'array' === $property_type || $array_depth > 0;
			$property_type = str_replace( '[]', '', $property_type );

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

		throw new JsonMapperException(
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'Unable to map ' . json_encode( $value ) . " to '$property->name'."
		);
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
			throw new JsonMapperException( "Unable to resolve uninstantiable \'{$property_type}\'." );
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
		$this->add_factory(
			'array',
			function ( $value ) {
				return new ArrayObject( $value );
			}
		);
	}

	/**
	 * @param string     $value_name
	 * @param Property[] $property_map
	 * @return Property|null
	 */
	private static function get_property_for_value( string $value_name, array $property_map ) {
		/** @var Property $property */
		foreach ( $property_map as $property_name => $property ) {
			if ( $property_name === $value_name ) {
				return $property;
			}
		}
		return null;
	}
}
