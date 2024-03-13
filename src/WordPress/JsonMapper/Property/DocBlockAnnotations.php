<?php

namespace WordPress\JsonMapper\Property;

use ReflectionProperty;
use WordPress\JsonMapper\ArrayInformation;
use WordPress\JsonMapper\ObjectWrapper;

class DocBlockAnnotations implements PropertyMapperInterface {

	const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)[ \t]+(?P<value>[\w\[\]\\\\|]*).*$/m';

	public function __construct() {}

	public function map_properties( ObjectWrapper $object_wrapper, PropertyMap $property_map ) {
		$property_map->merge( $this->compute_property_map( $object_wrapper ) );
	}

	/**
	 * @param ObjectWrapper $object
	 * @return PropertyMap
	 */
	private function compute_property_map( ObjectWrapper $object ): PropertyMap {
		$intermediate_property_map = new PropertyMap();
		foreach ( self::get_properties( $object ) as $property ) {
			$doc_block = $property->getDocComment();
			if ( false === $doc_block ) {
				continue;
			}

			$var = self::parse_var( $doc_block );
			if ( null === $var ) {
				continue;
			}

			$name        = $property->getName();
			$types       = explode( '|', $var );
			$is_nullable = in_array( 'null', $types, true );
			$types       = array_filter(
				$types,
				static function ( string $type ) {
					return 'null' !== $type;
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
				$type     = trim( $type );
				$is_array = substr( $type, -2 ) === '[]';

				if ( ! $is_array ) {
					$property->property_types[] = new PropertyType( $type, ArrayInformation::notAnArray() );
					continue;
				}

				$first_bracket_index = strpos( $type, '[' );
				$dimensions          = substr_count( $type, '[]' );

				if ( false !== $first_bracket_index ) {
					$type = substr( $type, 0, $first_bracket_index );
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
	 * @param string $doc_block
	 * @return string|null
	 */
	private static function parse_var( string $doc_block ): string {
		// Strip away the start "/**' and ending "*/".
		if ( strpos( $doc_block, '/**' ) === 0 ) {
			$doc_block = \substr( $doc_block, 3 );
		}
		if ( substr( $doc_block, -2 ) === '*/' ) {
			$doc_block = \substr( $doc_block, 0, -2 );
		}
		$doc_block = \trim( $doc_block );

		$var = null;
		if ( \preg_match_all( self::DOC_BLOCK_REGEX, $doc_block, $matches ) ) {
			for ( $x = 0, $max = count( $matches[0] ); $x < $max; $x++ ) {
				if ( 'var' === $matches['name'][ $x ] ) {
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
		$properties       = array();
		$reflection_class = $object->getReflectedObject();
		do {
			$properties = array_merge( $properties, $reflection_class->getProperties() );
		} while ( $reflection_class = $reflection_class->getParentClass() );
		return $properties;
	}
}
