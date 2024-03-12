<?php

namespace WordPress\JsonMapper\Evaluators;

use ReflectionProperty;
use WordPress\JsonMapper\ArrayInformation;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\ObjectWrapper;
use WordPress\JsonMapper\Property\Property;
use WordPress\JsonMapper\Property\PropertyMap;
use WordPress\JsonMapper\Property\PropertyType;

class DocBlockAnnotations implements JsonEvaluatorInterface {

	const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)[ \t]+(?P<value>[\w\[\]\\\\|]*).*$/m';

	public function __construct() {}

	public function evaluate(
		\stdClass $json,
		ObjectWrapper $object_wrapper,
		PropertyMap $property_map,
		JsonMapper $mapper
	) {
		$property_map->merge( $this->compute_property_map( $object_wrapper ) );
	}

	/**
	 * @param ObjectWrapper $object
	 * @return PropertyMap
	 */
	private function compute_property_map(ObjectWrapper $object ): PropertyMap {
		$intermediate_property_map = new PropertyMap();
		foreach ( self::get_properties( $object ) as $property ) {
			$docBlock = $property->getDocComment();
			if ( false === $docBlock ) {
				continue;
			}

			$var = self::parse_var( $docBlock );
			if ( null === $var ) {
				continue;
			}

			$name        = $property->getName();
			$types       = explode( '|', $var );
			$is_nullable = in_array( 'null', $types, true );
			$types       = array_filter(
				$types,
				static function ( string $type ) {
					return $type !== 'null';
				}
			);

			$property = new Property(
				$name,
				self::parse_visibility( $property ),
				$is_nullable
			);

			/* A union type that has one of its types defined as array is to complex to understand */
			if ( in_array( 'array', $types, true ) ) {
				$property->property_types[] = new PropertyType( 'mixed', ArrayInformation::singleDimension() );
				$intermediate_property_map->addProperty( $property );
				continue;
			}

			foreach ( $types as $type ) {
				$type          = trim( $type );
				$isAnArrayType = substr( $type, -2 ) === '[]';

				if ( ! $isAnArrayType ) {
					$property->property_types[] = new PropertyType( $type, ArrayInformation::notAnArray() );
					continue;
				}

				$initialBracketPosition = strpos( $type, '[' );
				$dimensions             = substr_count( $type, '[]' );

				if ( $initialBracketPosition !== false ) {
					$type = substr( $type, 0, $initialBracketPosition );
				}

				$property->property_types[] = new PropertyType( $type, ArrayInformation::multiDimension( $dimensions ) );
			}

			$intermediate_property_map->addProperty( $property );
		}

		return $intermediate_property_map;
	}

	/**
	 * @param ReflectionProperty $property
	 * @return string
	 */
	private static function parse_visibility( ReflectionProperty $property ): string {
		if ( $property->isPublic() ) {
			return 'public';
		}
		if ( $property->isProtected() ) {
			return 'protected';
		}
		return 'private';
	}

	/**
	 * @param string $docBlock
	 * @return string|null
	 */
	private static function parse_var( string $docBlock ): string {
		// Strip away the start "/**' and ending "*/"
		if ( strpos( $docBlock, '/**' ) === 0 ) {
			$docBlock = \substr( $docBlock, 3 );
		}
		if ( substr( $docBlock, -2 ) === '*/' ) {
			$docBlock = \substr( $docBlock, 0, -2 );
		}
		$docBlock = \trim( $docBlock );

		$var = null;
		if ( \preg_match_all( self::DOC_BLOCK_REGEX, $docBlock, $matches ) ) {
			for ( $x = 0, $max = count( $matches[0] ); $x < $max; $x++ ) {
				if ( $matches['name'][ $x ] === 'var' ) {
					$var = $matches['value'][ $x ];
				}
			}
		}

		return $var;
	}

	/**
	 * @param ObjectWrapper $object
	 * @return ReflectionProperty[]
	 */
	private static function get_properties( ObjectWrapper $object ): array {
		$properties      = array();
		$reflectionClass = $object->getReflectedObject();
		do {
			$properties = array_merge( $properties, $reflectionClass->getProperties() );
		} while ( $reflectionClass = $reflectionClass->getParentClass() );
		return $properties;
	}
}
