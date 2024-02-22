<?php

namespace WordPress\Blueprints;

use InvalidArgumentException;
use JsonMapper\JsonMapperBuilder;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\ModelInfo;

class BlueprintMapper {

	protected $mapper;

	public function __construct() {
		$this->configureMapper();
	}

	protected function configureMapper() {
		$resourceMap = [];
		foreach ( ModelInfo::getResourceDefinitionInterfaceImplementations() as $resourceClass ) {
			$resourceMap[ $resourceClass::DISCRIMINATOR ] = $resourceClass;
		}

		$classFactoryRegistry = \JsonMapper\Handler\FactoryRegistry::WithNativePhpClassesAdded();
		$classFactoryRegistry->addFactory(
			'ResourceDefinitionInterface',
			function ( $value ) use ( $resourceMap ) {
				if ( is_string( $value ) ) {
					return $value;
				}
				if ( ! isset( $value->resource ) ) {
					throw new InvalidArgumentException( "Resource type must be defined" );
				}
				if ( ! isset( $resourceMap[ $value->resource ] ) ) {
					throw new InvalidArgumentException( "Resource type {$value->resource} is not implemented" );
				}

				return $this->mapper->mapToClass( $value, $resourceMap[ $value->resource ] );
			}
		);


		$stepMap = [];
		foreach ( ModelInfo::getStepDefinitionInterfaceImplementations() as $class ) {
			$stepMap[ $class::DISCRIMINATOR ] = $class;
		}
		$classFactoryRegistry->addFactory(
			'StepDefinitionInterface',
			function ( $value ) use ( $stepMap ) {
				if ( ! isset( $value->step ) ) {
					throw new InvalidArgumentException( "Step must be defined" );
				}
				if ( ! isset( $stepMap[ $value->step ] ) ) {
					throw new InvalidArgumentException( "Step {$value->step} is not implemented" );
				}

				return $this->mapper->mapToClass( $value, $stepMap[ $value->step ] );
			}
		);

		$classFactoryRegistry->addFactory(
			\ArrayObject::class,
			function ( $value ) {
				return new \ArrayObject( $value );
			}
		);

		$this->mapper = JsonMapperBuilder::new()
			->withPropertyMapper( new \JsonMapper\Handler\PropertyMapper( $classFactoryRegistry ) )
			->withDocBlockAnnotationsMiddleware()
			->withTypedPropertiesMiddleware()
			->withNamespaceResolverMiddleware()
			->build();
	}

	/**
	 * Maps a parsed and validated JSON object to a Blueprint class instance.
	 *
	 * @param object $blueprint
	 *
	 * @return Blueprint
	 */
	public function map( object $blueprint ): Blueprint {
		return $this->mapper->mapToClass( $blueprint, Blueprint::class );
	}

}
