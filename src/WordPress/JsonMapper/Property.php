<?php

namespace WordPress\JsonMapper;

class Property {
	/** @var string */
	public $name;

	/** @var string[] */
	public $property_types;

	/** @var string */
	public $visibility;

	/** @var bool */
	public $is_nullable;

	public function __construct(
		string $name,
		string $visibility,
		bool $is_nullable,
		array $types = array()
	) {
		$this->name           = $name;
		$this->visibility     = $visibility;
		$this->is_nullable    = $is_nullable;
		$this->property_types = $types;
	}
}
