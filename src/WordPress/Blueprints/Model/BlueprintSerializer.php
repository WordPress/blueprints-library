<?php

namespace WordPress\Blueprints\Model;

use Swaggest\JsonSchema\Structure\ClassStructureContract;
use WordPress\Blueprints\Model\DataClass\Blueprint;

class BlueprintSerializer {

	public function toJson( Blueprint $blueprint ) {
		return json_encode( $blueprint );
	}

}
