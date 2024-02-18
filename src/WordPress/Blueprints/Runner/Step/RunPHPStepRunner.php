<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\RunPHPStep;
use WordPress\Blueprints\Progress\Tracker;

class RunPHPStepRunner extends BaseStepRunner {
	function run( RunPHPStep $input, Tracker $tracker ) {
		$tracker?->setCaption( "Running custom PHP code" );

		return $this->getRuntime()->evalPhpInSubProcess( $input->code );
	}
}
