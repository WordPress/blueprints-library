<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Progress\Tracker;

class CompiledBlueprint {

	public function __construct(
		public array $compiledSteps,
		public array $compiledResources,
		public Tracker $progressTracker,
		public Tracker $stepsProgressStage,
	) {
	}

}
