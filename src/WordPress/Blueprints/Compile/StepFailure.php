<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\BlueprintException;
use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;

class StepFailure extends BlueprintException implements StepResultInterface {

	/**
	 * @var \WordPress\Blueprints\Model\DataClass\StepDefinitionInterface
	 */
	public $step;
	public function __construct(
		StepDefinitionInterface $step,
		\Exception $cause
	) {
		$this->step = $step;
		parent::__construct( "Error when executing step $step->step", 0, $cause );
	}
}
