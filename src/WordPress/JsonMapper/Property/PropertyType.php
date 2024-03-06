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

	public function isArray(): bool {
		return $this->arrayInformation->isArray();
	}

	public function isMultiDimensionalArray(): bool {
		return $this->arrayInformation->isMultiDimensionalArray();
	}

	public function getArrayInformation(): ArrayInformation {
		return $this->arrayInformation;
	}

	public function jsonSerialize(): array {
		return array(
			'type'             => $this->type,
			'isArray'          => $this->arrayInformation->isArray(),
			'arrayInformation' => $this->arrayInformation,
		);
	}
}
