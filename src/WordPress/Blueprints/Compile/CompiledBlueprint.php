<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Progress\Tracker;

class CompiledBlueprint {
	public $compiledSteps;
	public $compiledResources;
	public $progressTracker;
	public $stepsProgressStage;

	/**
	 * @param $compiledSteps
	 * @param $compiledResources
	 * @param $progressTracker
	 * @param $stepsProgressStage
	 */
	public function __construct( $compiledSteps, $compiledResources, $progressTracker, $stepsProgressStage ) {
		$this->compiledSteps      = $compiledSteps;
		$this->compiledResources  = $compiledResources;
		$this->progressTracker    = $progressTracker;
		$this->stepsProgressStage = $stepsProgressStage;
	}
}
