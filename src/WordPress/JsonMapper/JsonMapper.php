<?php

namespace WordPress\JsonMapper;

use WordPress\JsonMapper\Evaluators\DocBlockAnnotations;
use WordPress\JsonMapper\Evaluators\NamespaceResolver;
use WordPress\JsonMapper\Evaluators\PropertyMapper;
use WordPress\JsonMapper\Property\PropertyMap;

class JsonMapper {
	private $evaluators;

	public function __construct() {}

	private function configure_evaluators() {
		if ( null === $this->evaluators ) {
			$this->evaluators = array(
				new DocBlockAnnotations(),
				new NamespaceResolver(),
				new PropertyMapper( $this ), // PropertyMapper has to be the last evaluator.
			);
		}
	}

	public function map_to_class( \stdClass $json, string $class ) {
		$this->configure_evaluators();

		$object_wrapper = new ObjectWrapper( null, $class );
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
