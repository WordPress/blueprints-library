<?php

namespace WordPress\Blueprints\Parsing;

use WordPress\Blueprints\BlueprintException;

class ParsingException extends BlueprintException {

	public function __construct( public readonly array $violations ) {
		parent::__construct( 'Blueprint parsing failed' );
	}

	public function getViolations() {
		return $this->violations;
	}
}
