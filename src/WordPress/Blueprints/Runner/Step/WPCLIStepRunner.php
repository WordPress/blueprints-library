<?php

namespace WordPress\Blueprints\Runner\Step;

use WordPress\Blueprints\Model\DataClass\WPCLIStep;

class WPCLIStepRunner extends BaseStepRunner {
	/**
	 * @param WPCLIStep $input
	 */
	function run( $input ) {
		$this->getRuntime()->runShellCommand( $input->command );
	}
}
