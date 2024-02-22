<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\StepDefinitionInterface;

class StepSuccess implements StepResultInterface {
	public function __construct( public StepDefinitionInterface $step, public $result ) {
	}
}
