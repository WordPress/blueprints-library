<?php

namespace WordPress\Blueprints\Compile;

class CompiledBlueprint {

	public function __construct(
		public array $compiledSteps,
		public array $compiledResources,
	) {
	}

}
