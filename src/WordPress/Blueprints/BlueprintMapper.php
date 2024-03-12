<?php

namespace WordPress\Blueprints;

use Closure;
use stdClass;
use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\Blueprints\Model\DataClass\ModelInfo;
use WordPress\JsonMapper\Evaluators\DocBlockAnnotations;
use WordPress\JsonMapper\Evaluators\NamespaceResolver;
use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\JsonMapperException;

class BlueprintMapper {
	/**
	 * @var JsonMapper
	 */
	private $mapper;

	/**
	 *
	 */
	public function __construct() {
		$json_evaluators  = array(
			new DocBlockAnnotations(),
			new NamespaceResolver(),
		);
		$custom_factories = array_merge(
			self::create_resource_factory(),
			self::create_steps_factory()
		);
		$this->mapper     = new JsonMapper( $json_evaluators, $custom_factories );
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
	 * @return array{ResourceDefinitionInterface: Closure}
	 */
	private static function create_resource_factory(): array {
		$resource_map = array();
		foreach ( ModelInfo::getResourceDefinitionInterfaceImplementations() as $resource_class ) {
			$resource_map[ $resource_class::DISCRIMINATOR ] = $resource_class;
		}

		return array(
			'ResourceDefinitionInterface' =>
								function ( $mapper, $value ) use ( $resource_map ) {
									if ( is_string( $value ) ) {
										return $value;
									}
									if ( ! isset( $value->resource ) ) {
										throw new JsonMapperException( 'Resource type must be defined' );
									}
									if ( ! isset( $resource_map[ $value->resource ] ) ) {
										throw new JsonMapperException( "Resource type {$value->resource} is not implemented" );
									}

									return $mapper->hydrate( $value, $resource_map[ $value->resource ] );
								},
		);
	}


	/**
	 * @return array{ResourceDefinitionInterface: Closure}
	 */
	private static function create_steps_factory(): array {
		$step_map = array();
		foreach ( ModelInfo::getStepDefinitionInterfaceImplementations() as $class ) {
			$step_map[ $class::DISCRIMINATOR ] = $class;
		}
		return array(
			'StepDefinitionInterface' =>
							function ( $mapper, $value ) use ( $step_map ) {
								if ( ! isset( $value->step ) ) {
									throw new JsonMapperException( 'Step must be defined' );
								}
								if ( ! isset( $step_map[ $value->step ] ) ) {
									throw new JsonMapperException( "Step {$value->step} is not implemented" );
								}

								return $mapper->hydrate( $value, $step_map[ $value->step ] );
							},
		);
	}
}
