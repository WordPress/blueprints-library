<?php

namespace WordPress\Blueprints\Compile;

use StepResult;
use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\StepInterface;

class StepFailure extends BlueprintException implements StepResult {

	public function __construct(
		public StepInterface $step,
		\Exception $cause
	) {
		parent::__construct( "Error when executing step $step->step", 0, $cause );
	}
}
