<?php

namespace WordPress\JsonMapper;

class Property {
	/** @var string */
	public $name;

	/** @var string[] */
	public $property_types;

	/** @var string */
	public $visibility;

	public function __construct(
		string $name,
		string $visibility,
		array $types = array()
	) {
		$this->name           = $name;
		$this->visibility     = $visibility;
		$this->property_types = $types;
	}
}
