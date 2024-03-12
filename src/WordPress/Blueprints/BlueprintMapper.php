<?php

namespace WordPress\Blueprints;


use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\ModelInfo;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\JsonMapperException;

class BlueprintMapper {
	private $mapper;

	private $custom_factories = array();

	public function __construct() {
		$this->mapper = new JsonMapper();
		$this->prepare_factory_for_resources();
		$this->prepare_factory_for_steps();
		$this->mapper->configure_evaluators( $this->custom_factories );
	}

	/**
	 * Maps a parsed and validated JSON object to a Blueprint class instance.
	 *
	 * @param $blueprint
	 * @return Blueprint
	 */
	public function map( $blueprint ) {
		return $this->mapper->map_to_class( $blueprint, Blueprint::class );
	}

	private function prepare_factory_for_resources() {
		$resource_map = array();
		foreach ( ModelInfo::getResourceDefinitionInterfaceImplementations() as $resourceClass ) {
			$resource_map[ $resourceClass::DISCRIMINATOR ] = $resourceClass;
		}

		$this->custom_factories['ResourceDefinitionInterface'] =
			function ( $value ) use ( $resource_map ) {
				if ( is_string( $value ) ) {
					return $value;
				}
				if ( ! isset( $value->resource ) ) {
					throw new JsonMapperException( 'Resource type must be defined' );
				}
				if ( ! isset( $resource_map[ $value->resource ] ) ) {
					throw new JsonMapperException( "Resource type {$value->resource} is not implemented" );
				}

				return $this->mapper->map_to_class( $value, $resource_map[ $value->resource ] );
			};
	}

	private function prepare_factory_for_steps() {
		$step_map = array();
		foreach ( ModelInfo::getStepDefinitionInterfaceImplementations() as $class ) {
			$step_map[ $class::DISCRIMINATOR ] = $class;
		}
		$this->custom_factories['StepDefinitionInterface'] =
			function ( $value ) use ( $step_map ) {
				if ( ! isset( $value->step ) ) {
					throw new JsonMapperException( 'Step must be defined' );
				}
				if ( ! isset( $step_map[ $value->step ] ) ) {
					throw new JsonMapperException( "Step {$value->step} is not implemented" );
				}

				return $this->mapper->map_to_class( $value, $step_map[ $value->step ] );
			};
	}
}
