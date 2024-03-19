<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\ResourceDefinitionInterface;
use WordPress\Blueprints\Progress\Tracker;

class CompiledResource {

	public $declaration;
	public $resource;
	public $progressTracker;

	public function __construct(
		$declaration,
		ResourceDefinitionInterface $resource,
		Tracker $progressTracker
	) {
		$this->declaration     = $declaration;
		$this->resource        = $resource;
		$this->progressTracker = $progressTracker;
	}
}
