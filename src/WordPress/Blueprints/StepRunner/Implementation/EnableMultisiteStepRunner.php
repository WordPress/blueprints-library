<?php

namespace WordPress\Blueprints\StepRunner\Implementation;

use WordPress\Blueprints\Model\DataClass\EnableMultisiteStep;
use WordPress\Blueprints\StepRunner\BaseStepRunner;

class EnableMultisiteStepRunner extends BaseStepRunner {
	/**
	 * @param EnableMultisiteStep $input
	 */
	function run( EnableMultisiteStep $input ) {
		throw new \LogicException( 'Not implemented yet' );

		// @TODO:
//		return $this->getRuntime()->runShellCommand(
//			[
//				'php',
//				'wp-cli.phar',
//				'core',
//				'multisite-convert',
//				// @TODO: Base path should come from the runtime, e.g.
//				//        Playground need to provide the /scope:0.892173/ value
//				'--base=/wordpress',
//			]
//		);
	}
}
