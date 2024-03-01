<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Progress\Tracker;

class CompiledResource {

	public function __construct(
		public $declaration,
		public ResourceDefinitionInterface $resource,
		public Tracker $progressTracker,
	) {
	}

}
