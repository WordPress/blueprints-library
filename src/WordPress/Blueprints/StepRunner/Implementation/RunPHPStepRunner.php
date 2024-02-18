<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\RunPHPStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepRunner\BaseStepRunner;

class RunPHPStepRunner extends BaseStepRunner {
	function run( RunPHPStep $input, Tracker $tracker ) {
		$tracker?->setCaption( "Running custom PHP code" );

		return $this->getRuntime()->evalPhpInSubProcess( $input->code );
	}
}
