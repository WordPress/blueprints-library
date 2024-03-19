<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\RunPHPStep;
use WordPress\Blueprints\Progress\Tracker;

class RunPHPStepRunner extends BaseStepRunner {
	/**
	 * @param \WordPress\Blueprints\Model\DataClass\RunPHPStep $input
	 * @param \WordPress\Blueprints\Progress\Tracker           $tracker
	 */
	function run( $input, $tracker ) {
		( $nullsafeVariable1 = $tracker ) ? $nullsafeVariable1->setCaption( 'Running custom PHP code' ) : null;

		return $this->getRuntime()->evalPhpInSubProcess( $input->code );
	}
}
