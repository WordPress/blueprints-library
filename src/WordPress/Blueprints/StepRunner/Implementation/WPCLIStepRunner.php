<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\WPCLIStep;
use WordPress\Blueprints\StepRunner\BaseStepRunner;

class WPCLIStepRunner extends BaseStepRunner {
	/**
	 * @param WPCLIStep $input
	 */
	function run( WPCLIStep $input ) {
		$this->getRuntime()->runShellCommand( $input->command );
	}
}
