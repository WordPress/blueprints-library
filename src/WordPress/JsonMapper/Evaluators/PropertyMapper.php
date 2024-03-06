<?php

namespace WordPress\JsonMapper\Evaluators;

use ReflectionClass;
use ReflectionMethod;
use WordPress\Blueprints\Model\DataClass\ModelInfo;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\JsonMapperException;
use WordPress\JsonMapper\ObjectWrapper;
use WordPress\JsonMapper\Property\Property;
use WordPress\JsonMapper\Property\PropertyMap;
use WordPress\JsonMapper\Property\PropertyType;
use function in_array;

class PropertyMapper implements JsonEvaluatorInterface {
	/**
	 * @var string[]
	 */
	private $scalar_types = array( 'string', 'bool', 'boolean', 'int', 'integer', 'double', 'float' );

	/**
	 * @var callable[]
	 */
	private $factories = array();

	/**
	 * @var JsonMapper
	 */
	private $mapper;

	public function __construct(
		JsonMapper $json_mapper
	) {
		$this->mapper = $json_mapper;
		$this->add_factories_for_native_php_classes();
		$this->add_factory_for_resources();
		$this->add_factory_for_steps();
		$this->add_factory_for_arrays();
	}

	public function evaluate(
		\stdClass $json,
		ObjectWrapper $object_wrapper,
		PropertyMap $property_map,
		JsonMapper $mapper
	) {
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
			return $this->mapper->map_to_class( $value, $type->getType() );
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
			\stdClass::class,
			static function ( $value ) {
				return (object) $value;
			}
		);
	}

	private function add_factory_for_resources() {
		$resourceMap = array();
		foreach ( ModelInfo::getResourceDefinitionInterfaceImplementations() as $resourceClass ) {
			$resourceMap[ $resourceClass::DISCRIMINATOR ] = $resourceClass;
		}
		$this->add_factory(
			'ResourceDefinitionInterface',
			function ( $value ) use ( $resourceMap ) {
				if ( is_string( $value ) ) {
					return $value;
				}
				if ( ! isset( $value->resource ) ) {
					throw new JsonMapperException( 'Resource type must be defined' );
				}
				if ( ! isset( $resourceMap[ $value->resource ] ) ) {
					throw new JsonMapperException( "Resource type {$value->resource} is not implemented" );
				}

				return $this->mapper->map_to_class( $value, $resourceMap[ $value->resource ] );
			}
		);
	}

	private function add_factory_for_steps() {
		$stepMap = array();
		foreach ( ModelInfo::getStepDefinitionInterfaceImplementations() as $class ) {
			$stepMap[ $class::DISCRIMINATOR ] = $class;
		}
		$this->add_factory(
			'StepDefinitionInterface',
			function ( $value ) use ( $stepMap ) {
				if ( ! isset( $value->step ) ) {
					throw new JsonMapperException( 'Step must be defined' );
				}
				if ( ! isset( $stepMap[ $value->step ] ) ) {
					throw new JsonMapperException( "Step {$value->step} is not implemented" );
				}

				return $this->mapper->map_to_class( $value, $stepMap[ $value->step ] );
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
