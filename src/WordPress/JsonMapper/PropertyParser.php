<?php

namespace WordPress\JsonMapper;

use ReflectionClass;
use ReflectionProperty;

/**
 *  Offers utilities for analyzing reflected classes for properties basing on reflections and their DocBlocks.
 */
class PropertyParser {

	const DOC_BLOCK_REGEX = '/@(?P<name>[A-Za-z_-]+)[ \t]+(?P<value>[\w\[\]\\\\|]*).*$/m';

	/**
	 * Private constructor.
	 */
	private function __construct() {}

	/**
	 * Analyzes the reflected class and returns a map of properties based on reflections and DocBlocks.
	 *
	 * @param ReflectionClass $reflection_class reflected class which DocBlocks are to be mapped to a property map.
	 * @return array<string,Property> the property map, key: property name
	 */
	public static function compute_property_map( ReflectionClass $reflection_class ) {
		$property_map = array();

		foreach ( self::get_properties( $reflection_class ) as $reflection_property ) {
			$property_map[ $reflection_property->getName() ] = new Property(
				$reflection_property->getName(),
				self::parse_visibility( $reflection_property ),
				self::parse_property_types( $reflection_property )
			);
		}

		return $property_map;
	}

	/**
	 * Returns property visibility as string. Only supports 'public', 'protected' and 'private'.
	 *
	 * @param ReflectionProperty $reflection_property reflected property to derive visibility from.
	 * @return string property visibility.
	 */
	private static function parse_visibility( ReflectionProperty $reflection_property ) {
		if ( $reflection_property->isPublic() ) {
			return 'public';
		}

		if ( $reflection_property->isProtected() ) {
			return 'protected';
		}

		return 'private';
	}

	/**
	 * Returns an array of property types parsed from the provided DocBlock. Filters out 'null'
	 * and removes Global Namespace Prefixes.
	 *
	 * Examples:
	 *
	 * For types: AnInterface[]|null will return: array('AnInterface[]')
	 *
	 * For types: string|int[][] will return: array('string', 'int[][]')
	 *
	 * For types: \ArrayObject will return array('ArrayObject')
	 *
	 * @param ReflectionProperty $reflection_property  reflected property to derive and parse DocBlock from.
	 * @return string[] array of property types, might be empty if no properties were listed in the DocBlock, or the
	 * DocBlock does not exist at all.
	 */
	private static function parse_property_types( ReflectionProperty $reflection_property ) {
		$doc_block = $reflection_property->getDocComment();

		if ( false === $doc_block ) {
			return array();
		}

		// Strip away the start "/**" and ending "*/".
		if ( strpos( $doc_block, '/**' ) === 0 ) {
			$doc_block = substr( $doc_block, 3 );
		}
		if ( substr( $doc_block, -2 ) === '*/' ) {
			$doc_block = substr( $doc_block, 0, -2 );
		}
		$doc_block = trim( $doc_block );

		$var = null;
		if ( preg_match_all( self::DOC_BLOCK_REGEX, $doc_block, $matches ) ) {
			for ( $x = 0, $max = count( $matches[0] ); $x < $max; $x++ ) {
				if ( 'var' === $matches['name'][ $x ] ) {
					$var = $matches['value'][ $x ];
				}
			}
		}

		if ( null === $var ) {
			return array();
		}

		$property_types = array();
		foreach ( explode( '|', $var ) as $property_type ) {
			// Filter out 'null' type.
			if ( 'null' === $property_type ) {
				continue;
			}
			// Return types without their Global Namespace Prefixes.
			$property_types[] = str_replace( '\\', '', $property_type );
		}

		return $property_types;
	}

	/**
	 * Returns an array of properties for the reflected class and all of its parents.
	 *
	 * @param ReflectionClass $reflection_class reflected class to recursively extract properties from.
	 * @return ReflectionProperty[] array of properties.
	 */
	private static function get_properties( ReflectionClass $reflection_class ) {
		$properties       = $reflection_class->getProperties();
		$reflected_parent = $reflection_class->getParentClass();

		while ( false !== $reflected_parent ) {
			$properties       = array_merge( $properties, $reflected_parent->getProperties() );
			$reflected_parent = $reflected_parent->getParentClass();
		}

		return $properties;
	}
}
