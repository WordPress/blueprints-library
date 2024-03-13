<?php

namespace WordPress\Blueprints;

use stdClass;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\ModelInfo;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\JsonMapperException;
use WordPress\JsonMapper\Property\DocBlockAnnotations;
use WordPress\JsonMapper\Property\NamespaceResolver;

class BlueprintMapper {
	/**
	 * @var JsonMapper
	 */
	private $mapper;

	/**
	 *
	 */
	public function __construct() {
		$property_mappers = array(
			new DocBlockAnnotations(),
			new NamespaceResolver(),
		);
		$custom_factories = array(
			'ResourceDefinitionInterface' => array( $this, 'resource_factory' ),
			'StepDefinitionInterface'     => array( $this, 'step_factory' ),
		);
		$this->mapper     = new JsonMapper( $property_mappers, $custom_factories );
	}

	/**
	 * Maps a parsed and validated JSON object to a Blueprint class instance.
	 *
	 * @param stdClass $blueprint a parsed and validated JSON object.
	 * @return Blueprint
	 */
	public function map( stdClass $blueprint ): Blueprint {
		return $this->mapper->hydrate( $blueprint, Blueprint::class );
	}

	/**
	 * @param $value
	 * @return object|string
	 * @throws JsonMapperException
	 */
	public function resource_factory( $value ) {
		$resource_map = array();
		foreach ( ModelInfo::getResourceDefinitionInterfaceImplementations() as $resource_class ) {
			$resource_map[ $resource_class::DISCRIMINATOR ] = $resource_class;
		}

		if ( is_string( $value ) ) {
			return $value;
		}
		if ( ! isset( $value->resource ) ) {
			throw new JsonMapperException( 'Resource type must be defined' );
		}
		if ( ! isset( $resource_map[ $value->resource ] ) ) {
			throw new JsonMapperException( "Resource type {$value->resource} is not implemented" );
		}

		return $this->mapper->hydrate( $value, $resource_map[ $value->resource ] );
	}

	/**
	 * @param $value
	 * @return object
	 * @throws JsonMapperException
	 */
	public function step_factory( $value ) {
		$step_map = array();
		foreach ( ModelInfo::getStepDefinitionInterfaceImplementations() as $class ) {
			$step_map[ $class::DISCRIMINATOR ] = $class;
		}

		if ( ! isset( $value->step ) ) {
			throw new JsonMapperException( 'Step must be defined' );
		}
		if ( ! isset( $step_map[ $value->step ] ) ) {
			throw new JsonMapperException( "Step {$value->step} is not implemented" );
		}

		return $this->mapper->hydrate( $value, $step_map[ $value->step ] );
	}
}
