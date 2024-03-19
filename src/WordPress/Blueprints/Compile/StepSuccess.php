<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;

class StepSuccess implements StepResultInterface {
	public $step;
	public $result;

	public function __construct( StepDefinitionInterface $step, $result ) {
		$this->step   = $step;
		$this->result = $result;
	}
}
