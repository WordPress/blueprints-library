<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;

class StepFailure extends BlueprintException implements StepResultInterface {

	public function __construct(
		public StepDefinitionInterface $step,
		\Exception $cause
	) {
		parent::__construct( "Error when executing step $step->step", 0, $cause );
	}
}
