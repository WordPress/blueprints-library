<?php

namespace WordPress\Blueprints\Model;

use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\Model\DataClass\Blueprint;

class BlueprintSerializer {

	/**
	 * @param \WordPress\Blueprints\Model\DataClass\Blueprint $blueprint
	 */
	public function toJson( $blueprint ) {
		return json_encode( $blueprint );
	}
}
