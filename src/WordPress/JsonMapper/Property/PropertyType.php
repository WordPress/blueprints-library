<?php

namespace WordPress\JsonMapper\Property;

use WordPress\JsonMapper\ArrayInformation;

class PropertyType {

	/** @var string */
	private $type;
	/** @var ArrayInformation */
	private $arrayInformation;

	public function __construct( string $type, ArrayInformation $isArray ) {
		$this->type             = $type;
		$this->arrayInformation = $isArray;
	}

	public function getType(): string {
		return $this->type;
	}

	public function getArrayInformation(): ArrayInformation {
		return $this->arrayInformation;
	}
}
