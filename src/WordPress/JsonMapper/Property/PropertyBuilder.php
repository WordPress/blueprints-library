<?php

namespace WordPress\JsonMapper\Property;

use WordPress\JsonMapper\ArrayInformation;

class PropertyBuilder {

	/** @var string */
	private $name;
	/** @var bool */
	private $isNullable;
	/** @var string */
	private $visibility;
	/** @var PropertyType[] */
	private $types = array();

	private function __construct() {}

	public static function new(): self {
		return new self();
	}

	public function build(): Property {
		return new Property(
			$this->name,
			$this->visibility,
			$this->isNullable,
			...$this->types
		);
	}

	public function setName( string $name ): self {
		$this->name = $name;
		return $this;
	}

	public function setTypes( PropertyType ...$types ): self {
		$this->types = $types;
		return $this;
	}

	public function addType( string $type, ArrayInformation $arrayInformation ): self {
		$this->types[] = new PropertyType( $type, $arrayInformation );
		return $this;
	}

	public function setIsNullable( bool $isNullable ): self {
		$this->isNullable = $isNullable;
		return $this;
	}

	public function setVisibility( string $visibility ): self {
		$this->visibility = $visibility;
		return $this;
	}
}
