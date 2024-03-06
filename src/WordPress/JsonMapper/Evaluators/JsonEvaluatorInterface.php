<?php

namespace WordPress\JsonMapper\Evaluators;

use WordPress\JsonMapper\JsonMapper;
use WordPress\JsonMapper\ObjectWrapper;
use WordPress\JsonMapper\Property\PropertyMap;

interface JsonEvaluatorInterface {

	public function evaluate(
		\stdClass $json,
		ObjectWrapper $object_wrapper,
		PropertyMap $property_map,
		JsonMapper $mapper
	);
}
