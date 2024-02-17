<?php

namespace WordPress\Blueprints\StepHandler;

use WordPress\Blueprints\Map;

abstract class BaseStepHandler {
	protected $resourceStreams;

	public function setResourceMap( Map $map ) {
		$this->resourceStreams = $map;
	}

	protected function getResource( $declaration ) {
		return $this->resourceStreams[ $declaration ];
	}
}
