<?php

namespace WordPress\Blueprints\Compile;

use WordPress\Blueprints\Model\DataClass\StepInterface;

class StepSuccess implements StepResultInterface {
	public function __construct( public StepInterface $step, public $result ) {
	}
}
