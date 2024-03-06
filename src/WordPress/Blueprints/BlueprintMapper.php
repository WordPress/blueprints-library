<?php

namespace WordPress\Blueprints;


use WordPress\Blueprints\Model\DataClass\Blueprint;
use WordPress\JsonMapper\JsonMapper;

class BlueprintMapper {
	protected $mapper;

	public function __construct() {
		$this->mapper = new JsonMapper();
	}


	/**
	 * Maps a parsed and validated JSON object to a Blueprint class instance.
	 *
	 * @param $blueprint
	 * @return Blueprint
	 */
	public function map( $blueprint ) {
		return $this->mapper->map_to_class( $blueprint, Blueprint::class );
	}
}