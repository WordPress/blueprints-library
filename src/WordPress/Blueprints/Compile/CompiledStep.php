<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;
use WordPress\Blueprints\Runner\Step\StepRunnerInterface;

class CompiledStep {

	public function __construct(
		public StepDefinitionInterface $step,
		public StepRunnerInterface $runner
	) {
	}

}
