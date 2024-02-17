<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\RunPHPStep;
use WordPress\Blueprints\Progress\Tracker;
use WordPress\Blueprints\StepHandler\BaseStepHandler;

class RunPHPStepHandler extends BaseStepHandler {
	function execute( RunPHPStep $input, Tracker $tracker = null ) {
		$tracker?->setCaption( "Running custom PHP code" );

		return $this->getRuntime()->evalPhpInSubProcess( $input->code );
	}
}
