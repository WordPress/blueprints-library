<?php

namespace WordPress\JsonMapper;

use WordPress\JsonMapper\Evaluators\DocBlockAnnotations;
use WordPress\JsonMapper\Evaluators\JsonEvaluatorInterface;
use WordPress\JsonMapper\Evaluators\NamespaceResolver;
use WordPress\JsonMapper\Evaluators\PropertyMapper;
use WordPress\JsonMapper\Property\PropertyMap;

class JsonMapper {
	/**
	 * @var JsonEvaluatorInterface[]
	 */
	private $evaluators = array();

	/**
	 * @param array $evaluators
	 * @param array $custom_factories
	 */
	public function __construct( array $evaluators = null, array $custom_factories = null ) {
		$this->evaluators[] = $evaluators;
		$this->evaluators[] = new PropertyMapper( $this, $custom_factories );
	}

	/**
	 * @param \stdClass $json
	 * @param string    $target
	 * @return object
	 */
	public function hydrate( \stdClass $json, string $target ) {
		$object_wrapper = new ObjectWrapper( null, $target );
		$property_map   = new PropertyMap();

		foreach ( $this->evaluators as $evaluator ) {
			$evaluator->evaluate(
				$json,
				$object_wrapper,
				$property_map,
				$this
			);
		}

		return $object_wrapper->getObject();
	}
}
