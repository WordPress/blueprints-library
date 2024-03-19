<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\Runner\Step\StepRunnerInterface;

class CompiledStep {

	public $step;
	public $runner;
	public $progressTracker;

	public function __construct(
		StepDefinitionInterface $step,
		StepRunnerInterface $runner,
		Tracker $progressTracker
	) {
		$this->step            = $step;
		$this->runner          = $runner;
		$this->progressTracker = $progressTracker;
	}
}
