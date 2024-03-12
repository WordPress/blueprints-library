<?php

namespace WordPress\JsonMapper\Evaluators;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use WordPress\JsonMapper\Import;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\ObjectWrapper;
use WordPress\JsonMapper\Property\Property;
use WordPress\JsonMapper\Property\PropertyMap;
use WordPress\JsonMapper\Property\PropertyType;
use WordPress\JsonMapper\UseNodeVisitor;

class NamespaceResolver {

	private $scalar_types = array( 'string', 'bool', 'boolean', 'int', 'integer', 'double', 'float' );

	public function __construct() {}

	public function map_properties( ObjectWrapper $object_wrapper, PropertyMap $property_map ) {
		foreach ( $this->fetchPropertyMapForObject( $object_wrapper, $property_map ) as $property ) {
			$property_map->addProperty( $property );
		}
	}

	private function fetchPropertyMapForObject( ObjectWrapper $object, PropertyMap $originalPropertyMap ): PropertyMap {
		$intermediatePropertyMap = new PropertyMap();
		$imports                 = self::getImports( $object->getReflectedObject() );

		/** @var Property $property */
		foreach ( $originalPropertyMap as $property ) {
			$types = $property->get_property_types();
			foreach ( $types as $index => $type ) {
				$types[ $index ] = $this->resolveSingleType( $type, $object, $imports );
			}
			$intermediatePropertyMap->addProperty( $property->as_builder()->setTypes( ...$types )->build() );
		}

		return $intermediatePropertyMap;
	}

	/** @return Import[] */
	private static function getImports( \ReflectionClass $class ): array {
		if ( ! $class->isUserDefined() ) {
			return array();
		}

		$filename = $class->getFileName();
		if ( $filename === false || \substr( $filename, -13 ) === "eval()'d code" ) {
			throw new \RuntimeException( "Class {$class->getName()} has no filename available" );
		}

		if ( $class->getParentClass() === false ) {
			return self::getImportsForFileName( $filename );
		}

		return array_unique(
			array_merge( self::getImportsForFileName( $filename ), self::getImports( $class->getParentClass() ) ),
			SORT_REGULAR
		);
	}

	/** @return Import[] */
	private static function getImportsForFileName( string $filename ): array {
		if ( ! \is_readable( $filename ) ) {
			throw new \RuntimeException( "Unable to read {$filename}" );
		}

		$contents = \file_get_contents( $filename );
		if ( $contents === false ) {
			throw new \RuntimeException( "Unable to read {$filename}" );
		}

		$parser = ( new ParserFactory() )->create( ParserFactory::PREFER_PHP7 );

		try {
			$ast = $parser->parse( $contents );
			if ( \is_null( $ast ) ) {
				throw new \Exception( "Failed to parse {$filename}" );
			}
		} catch ( \Throwable $e ) {
			throw new \Exception( "Failed to parse {$filename}" );
		}

		$traverser = new NodeTraverser();
		$visitor   = new UseNodeVisitor();
		$traverser->addVisitor( $visitor );
		$traverser->traverse( $ast );

		return $visitor->getImports();
	}


	/** @param Import[] $imports */
	private function resolveSingleType( PropertyType $type, ObjectWrapper $object, array $imports ): PropertyType {
		if ( $this->is_valid_scalar_type( $type ) ) {
			return $type;
		}

		$pos = strpos( $type->getType(), '\\' );
		if ( $pos === false ) {
			$pos = strlen( $type->getType() );
		}
		$nameSpacedFirstChunk = '\\' . substr( $type->getType(), 0, $pos );

		$matches = \array_filter(
			$imports,
			static function ( Import $import ) use ( $nameSpacedFirstChunk ) {
				if ( $import->hasAlias() && '\\' . $import->getAlias() === $nameSpacedFirstChunk ) {
					return true;
				}

				return $nameSpacedFirstChunk === \substr( $import->getImport(), -strlen( $nameSpacedFirstChunk ) );
			}
		);

		if ( count( $matches ) > 0 ) {
			$match = \array_shift( $matches );
			if ( $match->hasAlias() ) {
				$strippedType       = \substr( $type->getType(), strlen( $nameSpacedFirstChunk ) );
				$fullyQualifiedType = $match->getImport() . '\\' . $strippedType;
			} else {
				$strippedMatch      = \substr( $match->getImport(), 0, -strlen( $nameSpacedFirstChunk ) );
				$fullyQualifiedType = $strippedMatch . '\\' . $type->getType();
			}

			return new PropertyType( rtrim( $fullyQualifiedType, '\\' ), $type->getArrayInformation() );
		}

		$reflectedObject = $object->getReflectedObject();
		while ( true ) {
			if ( class_exists( $reflectedObject->getNamespaceName() . '\\' . $type->getType() ) ) {
				return new PropertyType(
					$reflectedObject->getNamespaceName() . '\\' . $type->getType(),
					$type->getArrayInformation()
				);
			}

			$reflectedObject = $reflectedObject->getParentClass();
			if ( ! $reflectedObject ) {
				break;
			}
		}

		return $type;
	}

	/**
	 * @param PropertyType $type
	 * @return bool
	 */
	private function is_valid_scalar_type( PropertyType $type ): bool {
		return in_array( $type->getType(), $this->scalar_types, true );
	}
}
