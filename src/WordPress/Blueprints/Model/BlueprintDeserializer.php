<?php

namespace WordPress\Blueprints\Model;

use Swaggest\JsonSchema\Structure\ClassStructureContract;

class BlueprintDeserializer {

	public function fromJson( $json ) {
		return $this->fromObject( json_decode( $json, false ) );
	}

	public function fromObject( object $data ) {
		return \WordPress\Blueprints\Model\Builder\BlueprintBuilder::import( $data )->toDataObject();
	}

}
