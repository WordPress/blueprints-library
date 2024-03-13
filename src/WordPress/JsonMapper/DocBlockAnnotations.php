<?php

namespace WordPress\JsonMapper;

use ReflectionClass;
use ReflectionProperty;

class DocBlockAnnotations {

	const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)[ \t]+(?P<value>[\w\[\]\\\\|]*).*$/m';

	private function __construct() {}

	/**
	 * @param ObjectWrapper $object
	 * @return array
	 */
	public static function compute_property_map( ObjectWrapper $object ): array {
		$property_map = array();
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
				$property->property_types[] = 'mixed';
				$property_map[] = $property ;
				continue;
			}

			foreach ( $types as $type ) {
				$type     = trim( $type );
				$is_array = substr( $type, -2 ) === '[]';

				if ( ! $is_array ) {
					$property->property_types[] = $type;
					continue;
				}

				$first_bracket_index = strpos( $type, '[' );

				if ( false !== $first_bracket_index ) {
					$type = substr( $type, 0, $first_bracket_index );
				}

				$property->property_types[] = $type;
			}

			$property_map[] = $property ;
		}

		return $property_map;
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
		$reflection_class = new ReflectionClass( $object->getObject() );
		do {
			$properties = array_merge( $properties, $reflection_class->getProperties() );
		} while ( $reflection_class = $reflection_class->getParentClass() );
		return $properties;
	}
}
