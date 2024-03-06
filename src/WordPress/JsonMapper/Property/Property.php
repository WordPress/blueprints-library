<?php

namespace WordPress\JsonMapper\Property;

class Property implements \JsonSerializable {
	/** @var string */
	private $name;

	/** @var PropertyType[] */
	private $property_types;

	/** @var string */
	public $visibility;

	/** @var bool */
	private $is_nullable;

	public function __construct(
		string $name,
		string $visibility,
		bool $is_nullable,
		PropertyType ...$types
	) {
		$this->name           = $name;
		$this->visibility     = $visibility;
		$this->is_nullable    = $is_nullable;
		$this->property_types = $types;
	}

	public function get_name(): string {
		return $this->name;
	}

	/** @return PropertyType[] */
	public function get_property_types(): array {
		return $this->property_types;
	}

	public function get_visibility(): string {
		return $this->visibility;
	}

	public function is_nullable(): bool {
		return $this->is_nullable;
	}

	public function as_builder(): PropertyBuilder {
		return PropertyBuilder::new()
			->setName( $this->name )
			->setTypes( ...$this->property_types )
			->setIsNullable( $this->is_nullable() )
			->setVisibility( $this->visibility );
	}

	// phpcs:ignore
	public function jsonSerialize(): array {
		return array(
			'name'       => $this->name,
			'types'      => $this->property_types,
			'visibility' => $this->visibility,
			'isNullable' => $this->is_nullable,
		);
	}
}
