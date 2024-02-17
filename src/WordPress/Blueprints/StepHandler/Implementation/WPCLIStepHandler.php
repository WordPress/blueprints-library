<?php

namespace WordPress\Blueprints\StepHandler\Implementation;

use WordPress\Blueprints\Model\DataClass\WPCLIStep;
use WordPress\Blueprints\StepHandler\BaseStepHandler;

class WPCLIStepHandler extends BaseStepHandler {
	/**
	 * @param WPCLIStep $input
	 */
	function execute( WPCLIStep $input ) {
		$this->getRuntime()->runShellCommand( $input->command );
	}
}
